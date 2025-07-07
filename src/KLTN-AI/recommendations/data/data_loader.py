# data/data_loader.py
import pandas as pd
import sqlalchemy
from sqlalchemy import create_engine
from dotenv import load_dotenv
import os
import logging
import sys
import pickle
import redis
import numpy as np

load_dotenv()
logging.basicConfig(stream=sys.stdout, level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Khởi tạo Redis client
def get_redis_client():
    try:
        client = redis.Redis(
            host=os.getenv('REDIS_HOST', 'localhost'),
            port=int(os.getenv('REDIS_PORT', 6379)),
            db=int(os.getenv('REDIS_DB', 0)),
            decode_responses=False
        )
        client.ping()
        logging.info("Kết nối Redis thành công")
        return client
    except redis.RedisError as e:
        logging.warning(f"Kết nối Redis thất bại: {e}. Tiếp tục mà không dùng cache.")
        return None

redis_client = get_redis_client()

def get_db_connection():
    try:
        db_user = os.getenv('DB_USER', 'root')
        db_password = os.getenv('DB_PASSWORD', 'root')
        db_server = os.getenv('DB_SERVER', '127.0.0.1')
        db_port = os.getenv('DB_PORT', '3306')
        db_name = os.getenv('DB_NAME', 'db_websitebansach')
        
        connection_string = f"mysql+pymysql://{db_user}:{db_password}@{db_server}:{db_port}/{db_name}"
        engine = create_engine(connection_string, connect_args={'connect_timeout': 10})
        return engine
    except Exception as e:
        logging.error(f"Kết nối cơ sở dữ liệu thất bại: {str(e)}")
        raise

def get_last_updated_time(table_name: str) -> pd.Timestamp | None:
    engine = get_db_connection()
    query = f"SELECT MAX(created_at) as last_updated FROM `{table_name}`;"
    try:
        with engine.connect() as connection:
            result_df = pd.read_sql(query, connection)
        
        if not result_df.empty and pd.notna(result_df['last_updated'].iloc[0]):
            return pd.to_datetime(result_df['last_updated'].iloc[0])
        return None
    except Exception as e:
        logging.error(f"Lỗi khi lấy thời gian cập nhật mới nhất cho {table_name}: {str(e)}")
        return None

def load_user_interactions() -> pd.DataFrame:
    engine = get_db_connection()
    query = """
    SELECT 
        `user_id`,
        `product_id`,
        `interaction_value`, 
        `created_at` 
    FROM `user_interaction`;
    """
    try:
        with engine.connect() as connection:
            df = pd.read_sql(query, connection)
        logging.info(f"Đã tải {len(df)} tương tác người dùng.")
        if not df.empty:
            df['user_id'] = df['user_id'].astype(int)
            df['product_id'] = df['product_id'].astype(int)
            df['interaction_value'] = df['interaction_value'].astype(np.float64)
            df['created_at'] = pd.to_datetime(df['created_at'])
        return df
    except Exception as e:
        logging.error(f"Lỗi khi tải tương tác người dùng: {str(e)}")
        return pd.DataFrame(columns=['user_id', 'product_id', 'interaction_value', 'created_at'])

def load_product_data() -> pd.DataFrame:
    engine = get_db_connection()
    query = """
    SELECT 
        `id`, `name`, `slug`, `short_description`, `long_description`, 
        `reguler_price`, `sale_price`, `discount_type`, `discount_value`, 
        `quantity`, `sold_count`, `image`, `images`, `publisher`, 
        `author`, `age`, `category_id`, `is_hot`, `created_at`
    FROM `products`;
    """
    try:
        with engine.connect() as connection:
            df = pd.read_sql(query, connection)
        logging.info(f"Đã tải {len(df)} sản phẩm.")
        if not df.empty:
            df['id'] = df['id'].astype(int)
            numeric_cols = ['reguler_price', 'sale_price', 'discount_value', 'quantity', 'sold_count', 'category_id', 'is_hot']
            for col in numeric_cols:
                if col in df.columns:
                    df[col] = pd.to_numeric(df[col], errors='coerce')
            df['created_at'] = pd.to_datetime(df['created_at'], errors='coerce')
        return df
    except Exception as e:
        logging.error(f"Lỗi khi tải dữ liệu sản phẩm: {str(e)}")
        return pd.DataFrame(columns=[
            'id', 'name', 'slug', 'short_description', 'long_description', 
            'reguler_price', 'sale_price', 'discount_type', 'discount_value', 
            'quantity', 'sold_count', 'image', 'images', 'publisher', 
            'author', 'age', 'category_id', 'is_hot', 'created_at'
        ])

def load_user_data() -> pd.DataFrame:
    engine = get_db_connection()
    query = "SELECT `id`, `name`, `email`, `created_at` FROM `users`;"
    try:
        with engine.connect() as connection:
            df = pd.read_sql(query, connection)
        logging.info(f"Đã tải {len(df)} người dùng cho metrics.")
        return df
    except Exception as e:
        logging.error(f"Lỗi khi tải dữ liệu người dùng cho metrics: {str(e)}")
        return pd.DataFrame(columns=['id', 'name', 'email', 'created_at'])

def load_user_item_matrix_for_training() -> pd.DataFrame:
    interactions_df = load_user_interactions()
    if interactions_df.empty:
        logging.warning("Không có dữ liệu tương tác để tạo ma trận user-item cho huấn luyện.")
        return pd.DataFrame()
    
    user_item_matrix = interactions_df.pivot_table(
        index='user_id',
        columns='product_id',
        values='interaction_value',
        fill_value=0.0
    ).astype(np.float64)
    logging.info(f"Ma trận user-item cho huấn luyện được tạo với shape {user_item_matrix.shape}")
    return user_item_matrix

def split_train_test(interactions_df: pd.DataFrame = None, test_ratio=0.2, time_based=True, save_to_file=True):
    """
    Chia dữ liệu tương tác thành tập huấn luyện và tập kiểm tra.
    - interactions_df: DataFrame chứa dữ liệu tương tác, nếu None thì load từ DB.
    - test_ratio: Tỷ lệ dữ liệu dùng cho tập kiểm tra (mặc định 20%).
    - time_based: Nếu True, chia theo thời gian (các tương tác mới nhất làm tập kiểm tra).
    - save_to_file: Lưu tập kiểm tra vào file.
    """
    logging.info("Bắt đầu chia dữ liệu thành tập huấn luyện và kiểm tra...")
    if interactions_df is None:
        interactions_df = load_user_interactions()
    
    if interactions_df.empty:
        logging.warning("Dữ liệu tương tác rỗng, trả về DataFrame rỗng.")
        return pd.DataFrame(), pd.DataFrame()
    
    try:
        if time_based:
            interactions_df = interactions_df.sort_values(by='created_at')
            cutoff_idx = int(len(interactions_df) * (1 - test_ratio))
            train_df = interactions_df.iloc[:cutoff_idx]
            test_df = interactions_df.iloc[cutoff_idx:]
        else:
            from sklearn.model_selection import train_test_split
            train_df, test_df = train_test_split(interactions_df, test_size=test_ratio, random_state=42)
        
        logging.info(f"Tập huấn luyện: {len(train_df)} tương tác, Tập kiểm tra: {len(test_df)} tương tác")
        
        if save_to_file:
            save_path = os.path.join(os.path.dirname(__file__), '..', 'data', 'test_interactions.pkl')
            with open(save_path, 'wb') as f:
                pickle.dump(test_df, f)
            logging.info(f"Đã lưu tập kiểm tra vào {save_path}")
            
            # # Lưu vào Redis nếu có
            # if redis_client:
            #     try:
            #         redis_client.setex("test_interactions", 86400 * 7, pickle.dumps(test_df))  # Lưu 7 ngày
            #         logging.info("Đã lưu tập kiểm tra vào Redis")
            #     except redis.RedisError as e:
            #         logging.warning(f"Lỗi khi lưu tập kiểm tra vào Redis: {e}")
        
        return train_df, test_df
    except Exception as e:
        logging.error(f"Lỗi khi chia dữ liệu: {str(e)}")
        return pd.DataFrame(), pd.DataFrame()