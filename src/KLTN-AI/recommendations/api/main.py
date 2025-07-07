import sys
import os
import logging
import threading
import pandas as pd
import numpy as np
from fastapi import FastAPI, HTTPException, Depends, Body
from pydantic import BaseModel, field_validator
from typing import List, Optional, Dict
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.decomposition import TruncatedSVD
from sklearn.feature_extraction.text import TfidfVectorizer
from scipy.sparse import csr_matrix
from datetime import datetime, timedelta
from apscheduler.schedulers.background import BackgroundScheduler
from apscheduler.triggers.cron import CronTrigger
from apscheduler.triggers.interval import IntervalTrigger
import pymysql
import json
import pickle
from collections import defaultdict
import redis
import subprocess
from functools import wraps
from dotenv import load_dotenv
from fastapi.responses import PlainTextResponse
import glob
from concurrent.futures import ThreadPoolExecutor
from sklearn.preprocessing import MinMaxScaler

from metrics.metrics import calculate_precision_recall_ndcg, calculate_diversity, calculate_coverage_at_k
from scripts.train_model import train_als_model
from data.data_loader import load_user_interactions, load_product_data, load_user_data, get_last_updated_time, split_train_test

app = FastAPI(title="Product Recommendation API")
logging.basicConfig(stream=sys.stdout, level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

load_dotenv()

BLACKLIST = set()

def get_redis_client():
    try:
        client = redis.Redis(
            host=os.getenv('REDIS_HOST', 'localhost'),
            port=int(os.getenv('REDIS_PORT', 6379)),
            db=int(os.getenv('REDIS_DB', 0)),
            decode_responses=True
        )
        client.ping()
        logging.info("Kết nối Redis thành công")
        return client
    except redis.RedisError as e:
        logging.warning(f"Kết nối Redis thất bại: {e}. Tiếp tục mà không dùng cache.")
        return None

redis_client = get_redis_client()

def check_mysql_connection():
    try:
        conn = pymysql.connect(
            host=os.getenv('DB_SERVER', '127.0.0.1'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', ''),
            database=os.getenv('DB_NAME', 'db_websitebansach'),
            port=int(os.getenv('DB_PORT', 3306)),
            connect_timeout=5
        )
        conn.close()
        logging.info("Kết nối MySQL thành công")
        return True
    except Exception as e:
        logging.error(f"Kết nối MySQL thất bại: {str(e)}")
        return False

class GlobalData:
    user_item_matrix = None
    products_df = None
    interactions_df = None
    popular_products = None
    user_similarity_svd = None
    item_similarity_svd = None
    content_similarity = None
    product_id_to_content_idx = None
    als_model = None
    als_model_user_ids = None
    als_model_item_ids = None
    als_model_item_id_to_model_idx = None
    current_user_item_csr_for_als = None
    current_user_id_to_csr_idx = None
    last_interaction_update = None
    last_product_update = None
    lock = threading.Lock()
    is_refreshing = False

    @staticmethod
    def refresh_data():
        with GlobalData.lock:
            if GlobalData.is_refreshing:
                logging.info("Đang làm mới dữ liệu, bỏ qua")
                return
            GlobalData.is_refreshing = True

        logging.info("Bắt đầu làm mới dữ liệu")
        try:
            interactions_df_new = load_user_interactions()
            products_df_new = load_product_data()

            if interactions_df_new.empty:
                logging.warning("Không tìm thấy tương tác. Sử dụng DataFrame rỗng.")
                interactions_df_new = pd.DataFrame(columns=['user_id', 'product_id', 'interaction_value'])
            if products_df_new.empty:
                logging.warning("Không tìm thấy sản phẩm. Sử dụng DataFrame rỗng.")
                products_df_new = pd.DataFrame(columns=['id', 'name', 'short_description', 'author'])

            train_df, test_df = split_train_test(interactions_df_new, test_ratio=0.2, time_based=True)

            user_item_matrix_new = pd.DataFrame()
            if not train_df.empty:
                try:
                    user_item_matrix_new = train_df.pivot_table(
                        index='user_id', columns='product_id', values='interaction_value', fill_value=0.0
                    ).astype(np.float64)
                except Exception as e:
                    logging.error(f"Lỗi khi tạo ma trận user-item: {e}. Sử dụng ma trận rỗng.")

            popular_products_new = pd.DataFrame(columns=['product_id', 'interaction_count'])
            if not train_df.empty:
                product_interactions = train_df.groupby('product_id').size().reset_index(name='interaction_count')
                popular_products_new = product_interactions.sort_values(by='interaction_count', ascending=False)
                popular_products_new = popular_products_new[~popular_products_new['product_id'].isin(BLACKLIST)]

            user_similarity_svd_new = np.array([[]])
            item_similarity_svd_new = np.array([[]])
            if not user_item_matrix_new.empty:
                try:
                    svd_user = TruncatedSVD(n_components=min(30, user_item_matrix_new.shape[1]-1 if user_item_matrix_new.shape[1]>1 else 1), random_state=42)
                    reduced_matrix_user = svd_user.fit_transform(user_item_matrix_new)
                    user_similarity_svd_new = cosine_similarity(reduced_matrix_user)

                    svd_item = TruncatedSVD(n_components=min(30, user_item_matrix_new.shape[0]-1 if user_item_matrix_new.shape[0]>1 else 1), random_state=42)
                    reduced_matrix_item = svd_item.fit_transform(user_item_matrix_new.T)
                    item_similarity_svd_new = cosine_similarity(reduced_matrix_item)
                except Exception as e:
                    logging.error(f"Lỗi khi tính toán tương đồng SVD: {e}")

            products_df_new = products_df_new.sort_values(by='id').reset_index(drop=True)
            product_id_to_content_idx_new = {pid: i for i, pid in enumerate(products_df_new['id'])}
            content_similarity_new = np.array([[]])
            if not products_df_new.empty:
                try:
                    tfidf = TfidfVectorizer(stop_words='english', min_df=1)
                    text_data = products_df_new['short_description'].fillna('') + ' ' + products_df_new['author'].fillna('')
                    if not text_data.empty and not text_data.str.isspace().all():
                        tfidf_matrix = tfidf.fit_transform(text_data)
                        content_similarity_new = cosine_similarity(tfidf_matrix)
                    else:
                        logging.warning("Dữ liệu văn bản cho tương đồng nội dung rỗng hoặc toàn khoảng trắng.")
                        content_similarity_new = np.identity(len(products_df_new))
                except Exception as e:
                    logging.error(f"Lỗi khi tính toán tương đồng nội dung: {e}")
                    content_similarity_new = np.identity(len(products_df_new))

            als_model_new = None
            als_model_user_ids_new = None
            als_model_item_ids_new = None
            als_model_item_id_to_model_idx_new = None
            current_user_item_csr_for_als_new = None
            current_user_id_to_csr_idx_new = None

            save_dir = os.getenv('MODEL_SAVE_DIR', './models')
            model_path = os.getenv('MODEL_PATH')
            if model_path and os.path.exists(model_path):
                model_files = [model_path]
            else:
                model_files = glob.glob(os.path.join(save_dir, 'als_model_v*.pkl'))
            
            if model_files:
                latest_model = max(model_files, key=os.path.getmtime)
                try:
                    with open(latest_model, 'rb') as f:
                        als_data = pickle.load(f)
                    als_model_new = als_data['model']
                    als_model_user_ids_new = als_data['user_ids']
                    als_model_item_ids_new = als_data['item_ids']
                    als_model_item_id_to_model_idx_new = {item_id: i for i, item_id in enumerate(als_model_item_ids_new)}
                    logging.info(f"Đã tải mô hình ALS từ {latest_model}")

                    if not user_item_matrix_new.empty and als_model_item_ids_new:
                        current_user_ids_list = user_item_matrix_new.index.tolist()
                        current_user_id_to_csr_idx_new = {uid: i for i, uid in enumerate(current_user_ids_list)}
                        aligned_df_for_als = pd.DataFrame(0.0, index=user_item_matrix_new.index, columns=als_model_item_ids_new, dtype=np.float64)
                        common_items = user_item_matrix_new.columns.intersection(als_model_item_ids_new)
                        if not common_items.empty:
                            aligned_df_for_als.loc[:, common_items] = user_item_matrix_new.loc[:, common_items].astype(np.float64)
                        current_user_item_csr_for_als_new = csr_matrix(aligned_df_for_als.values)
                        logging.info(f"Đã tạo current_user_item_csr_for_als với shape {current_user_item_csr_for_als_new.shape}")
                    elif als_model_item_ids_new:
                        current_user_item_csr_for_als_new = csr_matrix((0, len(als_model_item_ids_new)))
                        current_user_id_to_csr_idx_new = {}
                    else:
                        current_user_item_csr_for_als_new = csr_matrix((0,0))
                        current_user_id_to_csr_idx_new = {}
                except Exception as e:
                    logging.error(f"Lỗi khi tải hoặc xử lý mô hình ALS từ {latest_model}: {e}")
            else:
                logging.warning(f"Không tìm thấy file mô hình ALS trong thư mục {save_dir}.")
                train_als_model()
                GlobalData.refresh_data()

            with GlobalData.lock:
                GlobalData.interactions_df = train_df
                GlobalData.products_df = products_df_new
                GlobalData.user_item_matrix = user_item_matrix_new
                GlobalData.popular_products = popular_products_new
                GlobalData.user_similarity_svd = user_similarity_svd_new
                GlobalData.item_similarity_svd = item_similarity_svd_new
                GlobalData.content_similarity = content_similarity_new
                GlobalData.product_id_to_content_idx = product_id_to_content_idx_new
                GlobalData.als_model = als_model_new
                GlobalData.als_model_user_ids = als_model_user_ids_new
                GlobalData.als_model_item_ids = als_model_item_ids_new
                GlobalData.als_model_item_id_to_model_idx = als_model_item_id_to_model_idx_new
                GlobalData.current_user_item_csr_for_als = current_user_item_csr_for_als_new
                GlobalData.current_user_id_to_csr_idx = current_user_id_to_csr_idx_new
                GlobalData.last_interaction_update = get_last_updated_time('user_interaction')
                GlobalData.last_product_update = get_last_updated_time('products')
            logging.info("Làm mới dữ liệu hoàn tất.")

        except Exception as e:
            logging.error(f"Lỗi không xác định trong refresh_data: {e}")
            import traceback
            logging.error(traceback.format_exc())
        finally:
            with GlobalData.lock:
                GlobalData.is_refreshing = False

    @staticmethod
    def check_and_refresh():
        try:
            interaction_update = get_last_updated_time('user_interaction')
            product_update = get_last_updated_time('products')
            if (GlobalData.last_interaction_update is None or GlobalData.last_product_update is None or
                (interaction_update and GlobalData.last_interaction_update and interaction_update > GlobalData.last_interaction_update) or
                (product_update and GlobalData.last_product_update and product_update > GlobalData.last_product_update)):
                logging.info("Phát hiện thay đổi dữ liệu, bắt đầu làm mới trong nền")
                threading.Thread(target=GlobalData.refresh_data).start()
            else:
                logging.debug("Không phát hiện thay đổi dữ liệu để làm mới.")
        except Exception as e:
            logging.error(f"Lỗi khi kiểm tra cập nhật dữ liệu: {str(e)}")

    @staticmethod
    def train_and_refresh():
        with GlobalData.lock:
            try:
                logging.info("Bắt đầu huấn luyện tự động mô hình ALS")
                train_als_model()
                logging.info("Hoàn tất huấn luyện, bắt đầu làm mới dữ liệu")
                GlobalData.refresh_data()
                if redis_client:
                    try:
                        keys = redis_client.keys("recommendations:*")
                        if keys:
                            redis_client.delete(*keys)
                            logging.info(f"Đã xóa {len(keys)} key cache gợi ý từ Redis")
                    except redis.RedisError as e:
                        logging.warning(f"Lỗi khi xóa key cache gợi ý từ Redis: {e}")
            except Exception as e:
                logging.error(f"Lỗi trong quá trình huấn luyện tự động: {str(e)}")
                import traceback
                logging.error(traceback.format_exc())

if not check_mysql_connection():
    logging.critical("Kết nối MySQL thất bại. API không thể khởi động.")
    sys.exit(1)

GlobalData.refresh_data()

scheduler = BackgroundScheduler(daemon=True)
scheduler.add_job(GlobalData.check_and_refresh, IntervalTrigger(minutes=1), id="data_refresh_job", replace_existing=True)
scheduler.add_job(
    GlobalData.train_and_refresh,
    CronTrigger(hour=2, minute=0),
    id="train_model_job",
    replace_existing=True
)
try:
    scheduler.start()
    logging.info("APScheduler khởi động cho làm mới dữ liệu và huấn luyện tự động.")
except (KeyboardInterrupt, SystemExit):
    scheduler.shutdown()

class RecommendationRequest(BaseModel):
    user_id: int
    n_items: Optional[int] = 10
    method: str
    alpha: Optional[float] = 0.4
    beta: Optional[float] = 0.3
    gamma: Optional[float] = 0.15
    delta: Optional[float] = 0.1
    epsilon: Optional[float] = 0.05
    _weight_warning_logged = False  # Biến lớp để theo dõi log

    @field_validator('alpha', 'beta', 'gamma', 'delta', 'epsilon')
    def check_weights(cls, v, info):
        data = info.data
        field_name = info.field_name
        default_values = {
            'alpha': 0.4,
            'beta': 0.3,
            'gamma': 0.15,
            'delta': 0.1,
            'epsilon': 0.05
        }
        weights = [
            data.get('alpha', default_values['alpha']),
            data.get('beta', default_values['beta']),
            data.get('gamma', default_values['gamma']),
            data.get('delta', default_values['delta']),
            data.get('epsilon', default_values['epsilon'])
        ]
        weights[['alpha', 'beta', 'gamma', 'delta', 'epsilon'].index(field_name)] = v or default_values[field_name]
        total = sum(w or 0 for w in weights)
        if abs(total - 1.0) > 0.01 and not cls._weight_warning_logged:
            logging.warning(f"Tổng trọng số {total} không bằng 1.0, có thể ảnh hưởng đến kết quả gợi ý.")
            cls._weight_warning_logged = True
        return v

class RecommendationResponseItem(BaseModel):
    id: int
    score: float
    name: str
    slug: Optional[str] = None
    short_description: Optional[str] = None
    long_description: Optional[str] = None
    reguler_price: Optional[float] = None
    sale_price: Optional[float] = None
    discount_type: Optional[str] = None
    discount_value: Optional[float] = None
    quantity: Optional[int] = None
    sold_count: Optional[int] = None
    image: Optional[str] = None
    images: Optional[str] = None
    publisher: Optional[str] = None
    author: Optional[str] = None
    age: Optional[str] = None
    category_id: Optional[int] = None
    is_hot: Optional[int] = None
    created_at: Optional[str] = None

class ProductDetailResponse(BaseModel):
    id: int
    name: str
    slug: Optional[str] = None
    short_description: Optional[str] = None
    long_description: Optional[str] = None
    reguler_price: Optional[float] = None
    sale_price: Optional[float] = None
    discount_type: Optional[str] = None
    discount_value: Optional[float] = None
    quantity: Optional[int] = None
    sold_count: Optional[int] = None
    image: Optional[str] = None
    images: Optional[str] = None
    publisher: Optional[str] = None
    author: Optional[str] = None
    age: Optional[str] = None
    category_id: Optional[int] = None
    is_hot: Optional[int] = None
    created_at: Optional[str] = None

class EvaluationMetrics(BaseModel):
    method: str
    precision_at_k: float
    recall_at_k: float
    ndcg_at_k: float
    diversity_at_k: Optional[float] = None
    coverage_at_k: Optional[float] = None

def get_product_details(product_id: int) -> Optional[Dict]:
    """Lấy chi tiết sản phẩm theo ID, đảm bảo các trường số hợp lệ."""
    if GlobalData.products_df is None:
        return None
    product_series = GlobalData.products_df[GlobalData.products_df['id'] == product_id]
    if not product_series.empty:
        details = product_series.iloc[0].fillna('').to_dict()
        for key, value in details.items():
            if pd.isna(value) or value == '':
                details[key] = None
            elif key in ['reguler_price', 'sale_price', 'discount_value']:
                try:
                    details[key] = float(value) if value else None
                except (ValueError, TypeError):
                    details[key] = None
            elif key in ['id', 'quantity', 'sold_count', 'category_id', 'is_hot']:
                try:
                    details[key] = int(value) if value else None
                except (ValueError, TypeError):
                    details[key] = None
            else:
                details[key] = str(value) if value else None
        return details
    return None

def format_recommendations(recs: List[tuple[int, float]]) -> List[RecommendationResponseItem]:
    response = []
    if GlobalData.products_df is None:
        logging.warning("products_df chưa được tải, không thể định dạng gợi ý với chi tiết.")
        return [RecommendationResponseItem(id=pid, score=s, name=f"Product {pid}") for pid, s in recs]

    for product_id, score in recs:
        details = get_product_details(product_id)
        if details:
            try:
                item = RecommendationResponseItem(
                    id=int(details.get('id', product_id)),
                    score=float(score),
                    name=str(details.get('name', f"Product {product_id}")),
                    slug=details.get('slug'),
                    short_description=details.get('short_description'),
                    long_description=details.get('long_description'),
                    reguler_price=details.get('reguler_price'),
                    sale_price=details.get('sale_price'),
                    discount_type=details.get('discount_type'),
                    discount_value=details.get('discount_value'),
                    quantity=details.get('quantity'),
                    sold_count=details.get('sold_count'),
                    image=details.get('image'),
                    images=details.get('images'),
                    publisher=details.get('publisher'),
                    author=details.get('author'),
                    age=details.get('age'),
                    category_id=details.get('category_id'),
                    is_hot=details.get('is_hot'),
                    created_at=str(details.get('created_at')) if details.get('created_at') else None
                )
                response.append(item)
            except Exception as e:
                logging.error(f"Lỗi khi định dạng product_id {product_id} với chi tiết {details}: {e}")
        else:
            response.append(RecommendationResponseItem(id=product_id, score=score, name=f"Product {product_id} (Details N/A)"))
    return response

def get_popular_recommendations(n_items: int) -> List[tuple[int, float]]:
    if GlobalData.popular_products is None or GlobalData.popular_products.empty:
        return []
    top_popular = GlobalData.popular_products.head(n_items)
    if top_popular.empty:
        return []
    max_interactions = top_popular['interaction_count'].max()
    if max_interactions == 0:
        max_interactions = 1.0
    return [(row['product_id'], row['interaction_count'] / max_interactions) for _, row in top_popular.iterrows()]

def user_based_svd_recommendation(user_id: int, n_items: int = 10, k_neighbors: int = 10) -> List[tuple[int, float]]:
    if GlobalData.user_item_matrix is None or GlobalData.user_item_matrix.empty or \
       GlobalData.user_similarity_svd is None or GlobalData.user_similarity_svd.size == 0:
        logging.warning("SVD User-based: Dữ liệu chưa sẵn sàng.")
        return []
    if user_id not in GlobalData.user_item_matrix.index:
        logging.warning(f"SVD User-based: User {user_id} không có trong ma trận.")
        return []

    user_idx = GlobalData.user_item_matrix.index.get_loc(user_id)
    sim_scores = GlobalData.user_similarity_svd[user_idx]
    num_users = GlobalData.user_similarity_svd.shape[0]
    actual_k_neighbors = min(k_neighbors, num_users - 1 if num_users > 1 else 0)
    if actual_k_neighbors <= 0:
        return []

    sim_scores_copy = sim_scores.copy()
    sim_scores_copy[user_idx] = -np.inf
    top_k_indices = np.argsort(sim_scores_copy)[-actual_k_neighbors:]
    if not top_k_indices.size:
        return []

    top_k_sim_scores = sim_scores_copy[top_k_indices]
    weighted_sum = np.zeros(GlobalData.user_item_matrix.shape[1])
    sim_sum = np.zeros(GlobalData.user_item_matrix.shape[1])

    for i, neighbor_idx in enumerate(top_k_indices):
        neighbor_sim = top_k_sim_scores[i]
        if neighbor_sim <= 0:
            continue
        neighbor_ratings = GlobalData.user_item_matrix.iloc[neighbor_idx].values
        weighted_sum += neighbor_sim * neighbor_ratings
        sim_sum += neighbor_sim * (neighbor_ratings > 0)

    predicted_scores = np.zeros_like(weighted_sum)
    mask = sim_sum > 0
    predicted_scores[mask] = weighted_sum[mask] / sim_sum[mask]
    user_rated_items_mask = GlobalData.user_item_matrix.iloc[user_idx].values > 0
    predicted_scores[user_rated_items_mask] = 0

    recommendations = []
    for i, score in enumerate(predicted_scores):
        product_id = GlobalData.user_item_matrix.columns[i]
        if score > 0 and product_id not in BLACKLIST:
            recommendations.append((product_id, score))
    recommendations.sort(key=lambda x: x[1], reverse=True)
    return recommendations[:n_items]

def item_based_svd_recommendation(user_id: int, n_items: int = 10) -> List[tuple[int, float]]:
    if GlobalData.user_item_matrix is None or GlobalData.user_item_matrix.empty or \
       GlobalData.item_similarity_svd is None or GlobalData.item_similarity_svd.size == 0:
        logging.warning("SVD Item-based: Dữ liệu chưa sẵn sàng.")
        return []
    if user_id not in GlobalData.user_item_matrix.index:
        logging.warning(f"SVD Item-based: User {user_id} không có trong ma trận.")
        return []

    user_ratings_series = GlobalData.user_item_matrix.loc[user_id]
    interacted_items_indices = [GlobalData.user_item_matrix.columns.get_loc(pid) for pid in user_ratings_series[user_ratings_series > 0].index]
    if not interacted_items_indices:
        return []

    all_item_indices = np.arange(GlobalData.user_item_matrix.shape[1])
    items_to_predict_indices = np.setdiff1d(all_item_indices, interacted_items_indices)
    items_to_predict_ids = GlobalData.user_item_matrix.columns[items_to_predict_indices]
    valid_predict_indices_mask = [pid not in BLACKLIST for pid in items_to_predict_ids]
    items_to_predict_indices = items_to_predict_indices[valid_predict_indices_mask]

    if not items_to_predict_indices.size:
        return []

    predicted_scores = np.zeros(len(items_to_predict_indices))
    for i, target_item_idx in enumerate(items_to_predict_indices):
        weighted_sum = 0
        sim_sum = 0
        for interacted_item_idx in interacted_items_indices:
            user_rating_for_interacted = user_ratings_series.iloc[interacted_item_idx]
            similarity = GlobalData.item_similarity_svd[target_item_idx, interacted_item_idx]
            if similarity > 0:
                weighted_sum += similarity * user_rating_for_interacted
                sim_sum += similarity
        if sim_sum > 0:
            predicted_scores[i] = weighted_sum / sim_sum

    recommendations = []
    for i, score in enumerate(predicted_scores):
        if score > 0:
            product_id = GlobalData.user_item_matrix.columns[items_to_predict_indices[i]]
            recommendations.append((product_id, score))
    recommendations.sort(key=lambda x: x[1], reverse=True)
    return recommendations[:n_items]

def als_user_recommendation(user_id: int, n_items: int = 10) -> List[tuple[int, float]]:
    if not all([GlobalData.als_model, GlobalData.als_model_item_ids,
                GlobalData.current_user_item_csr_for_als is not None,
                GlobalData.current_user_id_to_csr_idx is not None]):
        logging.warning("ALS User: Mô hình hoặc dữ liệu liên quan chưa sẵn sàng.")
        return []

    recs = []
    try:
        if user_id in GlobalData.current_user_id_to_csr_idx:
            csr_user_idx = GlobalData.current_user_id_to_csr_idx[user_id]
            user_vector_for_als = GlobalData.current_user_item_csr_for_als[csr_user_idx]
            if user_id in GlobalData.als_model_user_ids:
                als_internal_user_idx = GlobalData.als_model_user_ids.index(user_id)
                raw_recs = GlobalData.als_model.recommend(
                    userid=als_internal_user_idx,
                    user_items=user_vector_for_als,
                    N=n_items,
                    filter_already_liked_items=True
                )
            else:
                raw_recs = GlobalData.als_model.recommend(
                    userid=0,
                    user_items=user_vector_for_als,
                    N=n_items,
                    filter_already_liked_items=True,
                    recalculate_user=True
                )
            logging.debug(f"ALS User raw_recs format: {raw_recs}")
            indices, scores = raw_recs
            for model_item_idx, score in zip(indices, scores):
                if model_item_idx < len(GlobalData.als_model_item_ids):
                    product_id = GlobalData.als_model_item_ids[int(model_item_idx)]
                    if product_id not in BLACKLIST:
                        recs.append((product_id, float(score)))
                else:
                    logging.warning(f"Chỉ số model_item_idx {model_item_idx} vượt quá kích thước als_model_item_ids.")
        else:
            logging.info(f"ALS User: User {user_id} không có tương tác hiện tại. Sử dụng fallback Content-based.")
            return content_based_recommendation(user_id, n_items)  # Fallback sang Content-based
    except Exception as e:
        logging.error(f"ALS User: Lỗi cho user {user_id}: {e}")
        import traceback
        logging.error(traceback.format_exc())
    return recs

def als_item_recommendation(user_id: int, n_items: int = 10) -> List[tuple[int, float]]:
    if not all([GlobalData.als_model, GlobalData.als_model_item_ids,
                GlobalData.als_model_item_id_to_model_idx, GlobalData.user_item_matrix is not None]):
        logging.warning("ALS Item: Mô hình hoặc dữ liệu liên quan chưa sẵn sàng.")
        return []
    if user_id not in GlobalData.user_item_matrix.index:
        logging.warning(f"ALS Item: User {user_id} không có trong ma trận.")
        return []

    user_interactions = GlobalData.user_item_matrix.loc[user_id]
    interacted_product_ids = user_interactions[user_interactions > 0].sort_values(ascending=False).head(5).index.tolist()
    if not interacted_product_ids:
        return []

    candidate_scores = defaultdict(float)
    for product_id in interacted_product_ids:
        if product_id in GlobalData.als_model_item_id_to_model_idx:
            model_item_idx = GlobalData.als_model_item_id_to_model_idx[product_id]
            try:
                similar_items_raw = GlobalData.als_model.similar_items(model_item_idx, N=n_items + len(interacted_product_ids) + 5)
                logging.debug(f"ALS Item similar_items_raw for product_id {product_id}: {similar_items_raw}")
                indices, scores = similar_items_raw
                for similar_model_idx, score in zip(indices, scores):
                    if similar_model_idx < len(GlobalData.als_model_item_ids):
                        similar_product_id = GlobalData.als_model_item_ids[int(similar_model_idx)]
                        if similar_product_id not in interacted_product_ids and similar_product_id not in BLACKLIST:
                            candidate_scores[similar_product_id] += float(score) * user_interactions[product_id]
                    else:
                        logging.warning(f"Chỉ số similar_model_idx {similar_model_idx} vượt quá kích thước als_model_item_ids.")
            except Exception as e:
                logging.warning(f"ALS Item: Không thể lấy sản phẩm tương tự cho product_id {product_id} (model_idx {model_item_idx}): {e}")
    
    sorted_candidates = sorted(candidate_scores.items(), key=lambda x: x[1], reverse=True)
    return sorted_candidates[:n_items]

def content_based_recommendation(user_id: int, n_items: int = 10) -> List[tuple[int, float]]:
    if not all([GlobalData.user_item_matrix is not None, not GlobalData.user_item_matrix.empty,
                GlobalData.products_df is not None, not GlobalData.products_df.empty,
                GlobalData.content_similarity is not None, GlobalData.content_similarity.size > 0,
                GlobalData.product_id_to_content_idx is not None]):
        logging.warning("Content-based: Dữ liệu chưa sẵn sàng.")
        return []
    if user_id not in GlobalData.user_item_matrix.index:
        logging.warning(f"Content-based: User {user_id} không có trong ma trận.")
        all_content_recs = []
        if GlobalData.products_df is not None and not GlobalData.products_df.empty:
            for idx in range(len(GlobalData.products_df)):
                product_id = GlobalData.products_df['id'].iloc[idx]
                if product_id not in BLACKLIST:
                    all_content_recs.append((product_id, 1.0))
            return sorted(all_content_recs, key=lambda x: x[1], reverse=True)[:n_items]
        return []

    user_interactions = GlobalData.user_item_matrix.loc[user_id]
    positive_interactions_ids = user_interactions[user_interactions > 0].index.tolist()
    if not positive_interactions_ids:
        logging.warning(f"Content-based: User {user_id} không có tương tác tích cực.")
        all_content_recs = []
        if GlobalData.products_df is not None and not GlobalData.products_df.empty:
            for idx in range(len(GlobalData.products_df)):
                product_id = GlobalData.products_df['id'].iloc[idx]
                if product_id not in BLACKLIST:
                    all_content_recs.append((product_id, 1.0))
            return sorted(all_content_recs, key=lambda x: x[1], reverse=True)[:n_items]

    all_item_scores = defaultdict(float)
    for interacted_pid in positive_interactions_ids:
        if interacted_pid in GlobalData.product_id_to_content_idx:
            content_idx = GlobalData.product_id_to_content_idx[interacted_pid]
            if content_idx < GlobalData.content_similarity.shape[0]:
                sim_vector = GlobalData.content_similarity[content_idx]
                for i, score in enumerate(sim_vector):
                    candidate_pid = GlobalData.products_df['id'].iloc[i]
                    if user_interactions.get(candidate_pid, 0) == 0 and candidate_pid not in BLACKLIST:
                        all_item_scores[candidate_pid] += score * user_interactions[interacted_pid]
            else:
                logging.warning(f"Content-based: content_idx {content_idx} cho pid {interacted_pid} vượt quá giới hạn ma trận content_similarity.")
    sorted_recs = sorted(all_item_scores.items(), key=lambda x: x[1], reverse=True)
    return sorted_recs[:n_items]

def hybrid_recommendation(user_id: int, req: RecommendationRequest) -> List[tuple[int, float]]:
    final_scores = defaultdict(float)
    num_components = 0

    logging.info(f"Hybrid recommendation cho user {user_id} với trọng số: "
                 f"alpha={req.alpha}, beta={req.beta}, gamma={req.gamma}, delta={req.delta}, epsilon={req.epsilon}")

    # User-based SVD
    if req.alpha > 0:
        user_svd_recs = user_based_svd_recommendation(user_id, req.n_items * 2)
        if user_svd_recs:
            scores = np.array([score for _, score in user_svd_recs]).reshape(-1, 1)
            scaler = MinMaxScaler()
            normalized_scores = scaler.fit_transform(scores).flatten()
            for (pid, _), norm_score in zip(user_svd_recs, normalized_scores):
                final_scores[pid] += req.alpha * norm_score
            num_components += 1
            logging.debug(f"User-based SVD trả về {len(user_svd_recs)} gợi ý.")

    # Item-based SVD
    if req.beta > 0:
        item_svd_recs = item_based_svd_recommendation(user_id, req.n_items * 2)
        if item_svd_recs:
            scores = np.array([score for _, score in item_svd_recs]).reshape(-1, 1)
            scaler = MinMaxScaler()
            normalized_scores = scaler.fit_transform(scores).flatten()
            for (pid, _), norm_score in zip(item_svd_recs, normalized_scores):
                final_scores[pid] += req.beta * norm_score
            num_components += 1
            logging.debug(f"Item-based SVD trả về {len(item_svd_recs)} gợi ý.")

    # Content-based
    if req.gamma > 0:
        content_recs = content_based_recommendation(user_id, req.n_items * 2)
        if content_recs:
            scores = np.array([score for _, score in content_recs]).reshape(-1, 1)
            scaler = MinMaxScaler()
            normalized_scores = scaler.fit_transform(scores).flatten()
            for (pid, _), norm_score in zip(content_recs, normalized_scores):
                final_scores[pid] += req.gamma * norm_score
            num_components += 1
            logging.debug(f"Content-based trả về {len(content_recs)} gợi ý.")

    # ALS User-based
    if req.delta > 0:
        als_user_recs = als_user_recommendation(user_id, req.n_items * 2)
        if als_user_recs:
            scores = np.array([score for _, score in als_user_recs]).reshape(-1, 1)
            scaler = MinMaxScaler()
            normalized_scores = scaler.fit_transform(scores).flatten()
            for (pid, _), norm_score in zip(als_user_recs, normalized_scores):
                final_scores[pid] += req.delta * norm_score
            num_components += 1
            logging.debug(f"ALS User-based trả về {len(als_user_recs)} gợi ý.")

    # ALS Item-based
    if req.epsilon > 0:
        als_item_recs = als_item_recommendation(user_id, req.n_items * 2)
        if als_item_recs:
            scores = np.array([score for _, score in als_item_recs]).reshape(-1, 1)
            scaler = MinMaxScaler()
            normalized_scores = scaler.fit_transform(scores).flatten()
            for (pid, _), norm_score in zip(als_item_recs, normalized_scores):
                final_scores[pid] += req.epsilon * norm_score
            num_components += 1
            logging.debug(f"ALS Item-based trả về {len(als_item_recs)} gợi ý.")

    if num_components == 0:
        logging.warning(f"Không có gợi ý nào từ bất kỳ phương pháp nào cho user {user_id}. Chuyển sang Content-based rồi kết hợp phổ biến.")
        content_recs = content_based_recommendation(user_id, req.n_items)
        if content_recs:
            for pid, score in content_recs:
                final_scores[pid] = score
            num_components += 1
        popular_recs = get_popular_recommendations(req.n_items)
        if popular_recs:
            for pid, score in popular_recs:
                if pid not in final_scores:
                    final_scores[pid] = score * 0.5  # Giảm trọng số cho sách phổ biến
            num_components += 1

    if num_components == 0:
        logging.error(f"Không tạo được gợi ý nào cho user {user_id} ngay cả với fallback.")
        return []

    # Chuẩn hóa điểm số cuối cùng
    if final_scores:
        scores = np.array(list(final_scores.values())).reshape(-1, 1)
        scaler = MinMaxScaler()
        normalized_scores = scaler.fit_transform(scores).flatten()
        final_scores = {pid: score for pid, score in zip(final_scores.keys(), normalized_scores)}

    sorted_recs = sorted(final_scores.items(), key=lambda x: x[1], reverse=True)
    if GlobalData.user_item_matrix is not None and user_id in GlobalData.user_item_matrix.index:
        user_interactions = GlobalData.user_item_matrix.loc[user_id]
        filtered_recs = [(pid, score) for pid, score in sorted_recs if user_interactions.get(pid, 0) == 0]
        return filtered_recs[:req.n_items]
    return sorted_recs[:req.n_items]

@app.get("/")
def read_root():
    return {"message": "API Gợi ý Sản phẩm đang chạy."}

@app.post("/refresh", summary="Kích hoạt làm mới dữ liệu thủ công")
async def trigger_refresh_data():
    if GlobalData.is_refreshing:
        raise HTTPException(status_code=429, detail="Làm mới dữ liệu đang được thực hiện.")
    threading.Thread(target=GlobalData.refresh_data).start()
    return {"message": "Quá trình làm mới dữ liệu đã bắt đầu trong nền."}

@app.post("/recommend", response_model=List[RecommendationResponseItem])
def get_recommendations_endpoint(request: RecommendationRequest):
    cache_key = f"recommendations:{request.user_id}:{request.n_items}:{request.method}:{request.alpha}:{request.beta}:{request.gamma}:{request.delta}:{request.epsilon}"
    cached_result = None
    if redis_client:
        try:
            cached_result = redis_client.get(cache_key)
            if cached_result:
                logging.info(f"Cache hit for user {request.user_id}")
                cached_data = json.loads(cached_result)
                return [RecommendationResponseItem(**item) for item in cached_data]
        except redis.RedisError as e:
            logging.warning(f"Lỗi khi truy cập Redis cache: {e}")
        except Exception as e:
            logging.error(f"Lỗi khi xử lý cache: {str(e)}")

    logging.info(f"Cache miss for user {request.user_id}")
    
    if not (1 <= request.n_items <= 100):
        raise HTTPException(status_code=400, detail="n_items phải từ 1 đến 100.")
    
    valid_methods = ["user_based_svd", "item_based_svd", "content_based", "als_user", "als_item", "hybrid"]
    if request.method not in valid_methods:
        raise HTTPException(status_code=400, detail=f"Phương thức không hợp lệ. Hỗ trợ: {valid_methods}")

    raw_recs: List[tuple[int, float]] = []
    if GlobalData.user_item_matrix is None and GlobalData.popular_products is None:
        raise HTTPException(status_code=503, detail="Dữ liệu cốt lõi chưa sẵn sàng. Hệ thống có thể đang khởi tạo.")

    user_has_interactions = GlobalData.user_item_matrix is not None and not GlobalData.user_item_matrix.empty and request.user_id in GlobalData.user_item_matrix.index
    
    if not user_has_interactions and request.method != "als_user":
        logging.info(f"User {request.user_id} không có tương tác. Sử dụng Content-based làm gợi ý ban đầu.")
        raw_recs = content_based_recommendation(request.user_id, request.n_items)
        if not raw_recs:
            logging.info(f"Content-based không đủ dữ liệu. Chuyển sang kết hợp với sách phổ biến.")
            popular_recs = get_popular_recommendations(request.n_items)
            if popular_recs:
                raw_recs = [(pid, score * 0.5) for pid, score in popular_recs]
    else:
        if request.method == "user_based_svd":
            raw_recs = user_based_svd_recommendation(request.user_id, request.n_items)
        elif request.method == "item_based_svd":
            raw_recs = item_based_svd_recommendation(request.user_id, request.n_items)
        elif request.method == "content_based":
            raw_recs = content_based_recommendation(request.user_id, request.n_items)
        elif request.method == "als_user":
            raw_recs = als_user_recommendation(request.user_id, request.n_items)
        elif request.method == "als_item":
            raw_recs = als_item_recommendation(request.user_id, request.n_items)
        elif request.method == "hybrid":
            raw_recs = hybrid_recommendation(request.user_id, request)
        
        if not raw_recs and user_has_interactions:
            logging.info(f"Phương thức {request.method} không trả về kết quả cho user {request.user_id}. Chuyển sang Content-based.")
            raw_recs = content_based_recommendation(request.user_id, request.n_items)
        elif not raw_recs and not user_has_interactions:
            logging.info(f"User {request.user_id} (không có tương tác) không trả về kết quả. Chuyển sang Content-based rồi phổ biến.")
            raw_recs = content_based_recommendation(request.user_id, request.n_items)
            if not raw_recs:
                popular_recs = get_popular_recommendations(request.n_items)
                if popular_recs:
                    raw_recs = [(pid, score * 0.5) for pid, score in popular_recs]

    if not raw_recs:
        logging.warning(f"Không tạo được gợi ý cho user {request.user_id} với phương thức {request.method}, và fallback cũng rỗng.")
        raise HTTPException(status_code=404, detail="Không thể tạo gợi ý.")

    formatted_response = format_recommendations(raw_recs)
    if not formatted_response:
        raise HTTPException(status_code=404, detail="Đã tạo gợi ý nhưng không tìm thấy chi tiết sản phẩm.")
    
    logging.info(f"Đã tạo thành công {len(formatted_response)} gợi ý cho user {request.user_id} bằng {request.method}.")
    
    if redis_client:
        try:
            redis_client.setex(
                cache_key,
                3600,
                json.dumps([item.dict() for item in formatted_response])
            )
        except redis.RedisError as e:
            logging.warning(f"Lỗi khi lưu cache vào Redis: {e}")
    
    return formatted_response

class ProductListItem(BaseModel):
    id: int
    name: str

@app.get("/products/list", response_model=List[ProductListItem], summary="Lấy danh sách ID và tên tất cả sản phẩm")
def list_products():
    if GlobalData.products_df is None or GlobalData.products_df.empty:
        raise HTTPException(status_code=503, detail="Dữ liệu sản phẩm chưa sẵn sàng.")
    product_list = GlobalData.products_df[['id', 'name']].copy()
    product_list['name'] = product_list['name'].fillna(f"Product ID: {product_list['id']}")
    return product_list.to_dict(orient='records')

@app.get("/products/{product_id}", response_model=ProductDetailResponse, summary="Lấy chi tiết của một sản phẩm cụ thể")
def get_single_product_details(product_id: int):
    details = get_product_details(product_id)
    if not details:
        raise HTTPException(status_code=404, detail=f"Không tìm thấy sản phẩm với ID {product_id}.")
    try:
        return ProductDetailResponse(**details)
    except Exception as e:
        logging.error(f"Lỗi khi tạo ProductDetailResponse cho {product_id}: {e}")
        raise HTTPException(status_code=500, detail=f"Lỗi khi xử lý chi tiết sản phẩm: {e}")

@app.get("/metrics/user-activity", summary="Lấy số liệu hoạt động người dùng")
def get_user_activity():
    if GlobalData.interactions_df is None:
        raise HTTPException(status_code=503, detail="Dữ liệu tương tác chưa sẵn sàng.")
    users_df = load_user_data()
    return {
        "total_db_users": len(users_df),
        "users_with_interactions": int(GlobalData.interactions_df['user_id'].nunique()),
        "total_interactions": int(len(GlobalData.interactions_df)),
    }

@app.get("/metrics/product-activity", summary="Lấy số liệu hoạt động sản phẩm")
def get_product_activity():
    if GlobalData.products_df is None:
        raise HTTPException(status_code=503, detail="Dữ liệu sản phẩm chưa sẵn sàng.")
    if GlobalData.interactions_df is None:
        raise HTTPException(status_code=503, detail="Dữ liệu tương tác chưa sẵn sàng.")
    return {
        "total_products": int(len(GlobalData.products_df)),
        "products_with_interactions": int(GlobalData.interactions_df['product_id'].nunique()),
        "avg_interactions_per_product_with_interaction": float(
            GlobalData.interactions_df.groupby('product_id').size().mean() if not GlobalData.interactions_df.empty else 0
        )
    }

@app.get("/metrics/evaluate_model", response_model=List[EvaluationMetrics], summary="Đánh giá hiệu suất các phương thức gợi ý")
async def evaluate_model(k: int = 10):
    try:
        if GlobalData.user_item_matrix is None or GlobalData.products_df is None:
            raise HTTPException(status_code=503, detail="Dữ liệu GlobalData chưa được khởi tạo.")

        test_path = os.path.join(os.path.dirname(__file__), '..', 'data', 'test_interactions.pkl')
        if os.path.exists(test_path):
            with open(test_path, 'rb') as f:
                test_interactions = pickle.load(f)
            logging.info(f"Đã tải tập kiểm tra từ {test_path}")
        else:
            raise HTTPException(status_code=404, detail="Không tìm thấy tập kiểm tra.")

        if test_interactions.empty:
            raise HTTPException(status_code=404, detail="Tập kiểm tra rỗng.")

        methods = ["user_based_svd", "item_based_svd", "content_based", "als_user", "als_item", "hybrid"]
        results = []
        recommendations_dict = {method: {} for method in methods}
        total_items = set(GlobalData.products_df['id'].unique())

        def evaluate_user(user_id, method, k):
            try:
                relevant_items = test_interactions[test_interactions['user_id'] == user_id]['product_id'].tolist()
                request = RecommendationRequest(user_id=user_id, n_items=k, method=method)
                recommendations = get_recommendations_endpoint(request)
                if not isinstance(recommendations, list):
                    logging.warning(f"Kết quả gợi ý không hợp lệ cho user {user_id}, method {method}")
                    return 0, 0, 0, None, []
                recommended_ids = [rec.id for rec in recommendations]
                recommendations_dict[method][user_id] = recommended_ids
                precision, recall, ndcg = calculate_precision_recall_ndcg(recommended_ids, relevant_items, k)
                diversity = None
                if method in ["content_based", "hybrid"] and GlobalData.content_similarity is not None:
                    diversity = calculate_diversity(recommended_ids, GlobalData.content_similarity, GlobalData.product_id_to_content_idx, k)
                return precision, recall, ndcg, diversity, recommended_ids
            except Exception as e:
                logging.error(f"Lỗi khi đánh giá user {user_id}, method {method}: {str(e)}")
                return 0, 0, 0, None, []

        for method in methods:
            precisions, recalls, ndcgs, diversities = [], [], [], []
            users = test_interactions['user_id'].unique()[:100]
            with ThreadPoolExecutor(max_workers=4) as executor:
                results_user = list(executor.map(lambda u: evaluate_user(u, method, k), users))
            for precision, recall, ndcg, diversity, _ in results_user:
                precisions.append(precision)
                recalls.append(recall)
                ndcgs.append(ndcg)
                diversities.append(diversity)

            avg_precision = np.mean(precisions) if precisions else 0
            avg_recall = np.mean(recalls) if recalls else 0
            avg_ndcg = np.mean(ndcgs) if ndcgs else 0
            avg_diversity = np.mean([d for d in diversities if d is not None]) if any(d is not None for d in diversities) else None

            coverage = calculate_coverage_at_k(recommendations_dict[method], total_items, k)

            results.append(EvaluationMetrics(
                method=method,
                precision_at_k=avg_precision,
                recall_at_k=avg_recall,
                ndcg_at_k=avg_ndcg,
                diversity_at_k=avg_diversity,
                coverage_at_k=coverage
            ))

        logging.info(f"Hoàn tất đánh giá cho k={k}")
        return results
    except Exception as e:
        logging.error(f"Lỗi khi đánh giá mô hình: {str(e)}")
        import traceback
        logging.error(traceback.format_exc())
        raise HTTPException(status_code=500, detail=f"Lỗi khi đánh giá mô hình: {str(e)}")

@app.get("/metrics/interaction_matrix", summary="Lấy ma trận tương tác user-item")
async def get_interaction_matrix():
    if GlobalData.user_item_matrix is None or GlobalData.user_item_matrix.empty:
        raise HTTPException(status_code=503, detail="Ma trận tương tác chưa sẵn sàng.")
    matrix_data = GlobalData.user_item_matrix.to_dict(orient='index')
    return {
        "users": list(matrix_data.keys()),
        "items": list(GlobalData.user_item_matrix.columns),
        "values": [[matrix_data[user][item] for item in GlobalData.user_item_matrix.columns] for user in matrix_data]
    }

@app.get("/training/status", response_class=PlainTextResponse)
def get_training_status():
    try:
        with open("training_progress.txt", "r", encoding="utf-8") as f:
            progress = f.read().strip()
        return progress if progress else "0%"
    except FileNotFoundError:
        return "0%"
    except Exception as e:
        logging.error(f"Lỗi khi đọc tiến trình huấn luyện: {e}")
        return "0%"

@app.post("/train", summary="Kích hoạt huấn luyện mô hình ALS")
def trigger_training():
    try:
        script_path = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "scripts", "train_model.py"))
        
        if not os.path.exists(script_path):
            raise FileNotFoundError(f"Không tìm thấy file: {script_path}")

        logging.info(f"Đang chạy script huấn luyện: {script_path}")
        subprocess.Popen([sys.executable, script_path])
        
        if redis_client:
            try:
                keys = redis_client.keys("recommendations:*")
                if keys:
                    redis_client.delete(*keys)
                    logging.info(f"Đã xóa {len(keys)} key cache gợi ý từ Redis")
            except redis.RedisError as e:
                logging.warning(f"Lỗi khi xóa key cache gợi ý từ Redis: {e}")
        
        return {"message": "Huấn luyện mô hình ALS đã được kích hoạt."}
    except Exception as e:
        logging.error(f"Lỗi khi kích hoạt huấn luyện: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/clear_cache", summary="Xóa cache gợi ý trong Redis")
async def clear_cache():
    if redis_client:
        try:
            keys = redis_client.keys("recommendations:*")
            if keys:
                redis_client.delete(*keys)
                logging.info(f"Đã xóa {len(keys)} key cache gợi ý từ Redis")
            return {"message": f"Đã xóa {len(keys)} key cache gợi ý."}
        except redis.RedisError as e:
            logging.warning(f"Lỗi khi xóa cache gợi ý: {e}")
            raise HTTPException(status_code=500, detail="Lỗi khi xóa cache")
    return {"message": "Không có Redis client, không cần xóa cache."}

@app.get("/training/schedule", summary="Xem lịch trình huấn luyện")
async def get_training_schedule():
    job = scheduler.get_job("train_model_job")
    if job:
        return {"next_run": str(job.next_run_time), "trigger": str(job.trigger)}
    return {"message": "Không tìm thấy job huấn luyện"}

@app.post("/select_model")
def select_model(model_path: dict = Body(...)):
    try:
        model_path = model_path.get("model_path")
        if not os.path.exists(model_path):
            logging.warning(f"Mô hình {model_path} không tồn tại. Kích hoạt huấn luyện mới.")
            train_als_model()
            GlobalData.refresh_data()
            raise HTTPException(status_code=404, detail="Mô hình không tồn tại, đã kích hoạt huấn luyện mới.")
        
        with open(model_path, 'rb') as f:
            als_data = pickle.load(f)
        
        with GlobalData.lock:
            GlobalData.als_model = als_data['model']
            GlobalData.als_model_user_ids = als_data['user_ids']
            GlobalData.als_model_item_ids = als_data['item_ids']
            GlobalData.als_model_item_id_to_model_idx = {item_id: i for i, item_id in enumerate(als_data['item_ids'])}
            logging.info(f"Đã tải mô hình từ {model_path}")
        
        if redis_client:
            try:
                keys = redis_client.keys("recommendations:*")
                if keys:
                    redis_client.delete(*keys)
                    logging.info(f"Đã xóa {len(keys)} key cache gợi ý từ Redis")
            except redis.RedisError as e:
                logging.warning(f"Lỗi khi xóa key cache gợi ý từ Redis: {e}")
        
        return {"message": f"Đã chọn mô hình từ {os.path.basename(model_path)}"}
    except Exception as e:
        logging.error(f"Lỗi khi chọn mô hình: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/delete_model")
def delete_model(model_path: dict = Body(...)):
    try:
        model_path = model_path.get("model_path")
        if not os.path.exists(model_path):
            raise HTTPException(status_code=404, detail="Mô hình không tồn tại.")

        os.remove(model_path)
        logging.info(f"Đã xóa mô hình: {os.path.basename(model_path)}")

        metadata_file = model_path.replace(".pkl", "_metadata.json")
        if os.path.exists(metadata_file):
            os.remove(metadata_file)
            logging.info(f"Đã xóa metadata: {os.path.basename(metadata_file)}")

        # Làm mới dữ liệu để cập nhật mô hình
        GlobalData.refresh_data()
        
        return {"message": f"Đã xóa mô hình: {os.path.basename(model_path)}"}
    except Exception as e:
        logging.error(f"Lỗi khi xóa mô hình: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/training/model_list")
def get_model_list():
    try:
        save_dir = os.getenv('MODEL_SAVE_DIR', './models')
        model_files = glob.glob(os.path.join(save_dir, 'als_model_v*.pkl'))
        if not model_files:
            return {"models": []}
        
        model_list = [{"path": f, "name": os.path.basename(f), "timestamp": os.path.getmtime(f)} for f in model_files]
        model_list.sort(key=lambda x: x["timestamp"], reverse=True)  # Sắp xếp theo thời gian mới nhất
        return {"models": model_list}
    except Exception as e:
        logging.error(f"Lỗi khi lấy danh sách mô hình: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    app.run(debug=True)