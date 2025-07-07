import os
import pickle
from implicit.als import AlternatingLeastSquares
from scipy.sparse import csr_matrix
import logging
import sys
from threadpoolctl import threadpool_limits
from datetime import datetime
import glob
import json

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from data.data_loader import load_user_interactions, split_train_test

logging.basicConfig(stream=sys.stdout, level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def update_progress(percent):
    """Cập nhật tiến trình huấn luyện vào file."""
    with open("training_progress.txt", "w", encoding="utf-8") as f:
        f.write(str(percent))

def get_next_version(save_dir):
    """Tìm số phiên bản tiếp theo dựa trên các file mô hình hiện có."""
    existing_models = glob.glob(os.path.join(save_dir, 'als_model_v*.pkl'))
    if not existing_models:
        return 1
    versions = [int(os.path.basename(f).split('_v')[1].split('_')[0])
                for f in existing_models if 'als_model_v' in f]
    return max(versions) + 1 if versions else 1

def cleanup_old_models(save_dir, max_models=5):
    """Xóa các mô hình cũ, giữ lại max_models mô hình mới nhất."""
    model_files = glob.glob(os.path.join(save_dir, 'als_model_v*.pkl'))
    if len(model_files) > max_models:
        model_files.sort(key=os.path.getmtime)
        for old_model in model_files[:-max_models]:
            os.remove(old_model)
            logging.info(f"Đã xóa mô hình cũ: {old_model}")

def train_als_model():
    """Huấn luyện mô hình ALS và lưu với tên theo phiên bản và ngày giờ."""
    logging.info("Bắt đầu huấn luyện mô hình ALS...")
    try:
        update_progress("0%")
        interactions_df = load_user_interactions()
        train_df, _ = split_train_test(interactions_df, test_ratio=0.2, time_based=True, save_to_file=True)
        if train_df.empty:
            logging.warning("Tập huấn luyện rỗng. Bỏ qua huấn luyện mô hình ALS.")
            update_progress("0%")
            return
        logging.info("Tạo ma trận user-item...")
        user_item_matrix = train_df.pivot_table(
            index='user_id',
            columns='product_id',
            values='interaction_value',
            fill_value=0
        )
        user_item_csr = csr_matrix(user_item_matrix.values)
        user_ids_list = user_item_matrix.index.tolist()
        item_ids_list = user_item_matrix.columns.tolist()
        update_progress("20%")
        logging.info("Bắt đầu huấn luyện ALS...")
        model = AlternatingLeastSquares(
            factors=32,
            regularization=0.1,
            iterations=15,
            random_state=42,
            use_gpu=False
        )
        log_dir = "./logs"
        os.makedirs(log_dir, exist_ok=True)
        log_file = os.path.join(log_dir, "training_progress.log")
        with threadpool_limits(limits=1, user_api="blas"):
            model.fit(user_item_csr, show_progress=True)
            for i in range(15):
                progress = 20 + (i + 1) * (80 / 15)
                update_progress(f"{progress:.2f}%")
        with open(log_file, "a", encoding="utf-8") as f:
            f.write("100.00% hoàn thành - Huấn luyện xong\n")
        update_progress("100.00%")
        logging.info("Hoàn tất huấn luyện mô hình ALS.")
        save_dir = os.getenv('MODEL_SAVE_DIR', './models')
        os.makedirs(save_dir, exist_ok=True)
        # Tạo tên file theo phiên bản và ngày giờ
        version = get_next_version(save_dir)
        timestamp = datetime.now().strftime("%Y%m%d_%H%M")
        save_path = os.path.join(save_dir, f'als_model_v{version}_{timestamp}.pkl')
        # Lưu mô hình
        with open(save_path, 'wb') as f:
            pickle.dump({
                'model': model,
                'user_ids': user_ids_list,
                'item_ids': item_ids_list
            }, f)
        logging.info(f"Đã lưu mô hình ALS và ánh xạ vào {save_path}")
        # Lưu metadata
        metadata = {
            'version': version,
            'timestamp': timestamp,
            'factors': 32,
            'regularization': 0.1,
            'iterations': 15,
            'save_path': save_path
        }
        metadata_path = os.path.join(save_dir, f'als_model_v{version}_{timestamp}_metadata.json')
        with open(metadata_path, 'w') as f:
            json.dump(metadata, f, indent=2)
        logging.info(f"Đã lưu metadata mô hình vào {metadata_path}")
        # Xóa mô hình cũ
        cleanup_old_models(save_dir, max_models=5)
    except Exception as e:
        logging.error(f"Lỗi khi huấn luyện mô hình ALS: {str(e)}")
        import traceback
        logging.error(traceback.format_exc())
        update_progress("0%")

if __name__ == "__main__":
    from dotenv import load_dotenv
    dotenv_path = os.path.join(os.path.dirname(__file__), '..', '.env')
    if os.path.exists(dotenv_path):
        load_dotenv(dotenv_path)
        logging.info(f"Đã tải file .env từ: {dotenv_path}")
    else:
        logging.warning(f"Không tìm thấy file .env tại: {dotenv_path}")
        
    train_als_model()