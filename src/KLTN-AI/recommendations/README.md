# Hệ thống Đề xuất Sách Trực Tuyến - Chuyên sâu (Cập nhật)

Dự án này là một giải pháp toàn diện cho việc xây dựng hệ thống gợi ý sách thông minh. Mục tiêu là nâng cao trải nghiệm người dùng và thúc đẩy doanh số bằng cách giới thiệu những đầu sách phù hợp. Phiên bản này đã được cập nhật với mô hình ALS và cải tiến các thuật toán gợi ý.

## I. Tính năng chính

### 1. Cơ chế Đề xuất Sách Thông Minh
* **Lọc Cộng tác Dựa trên Người dùng (User-based SVD):** Sử dụng TruncatedSVD và cosine similarity. (Phương thức: `user_based_svd`)
* **Lọc Cộng tác Dựa trên Sản phẩm (Item-based SVD):** Sử dụng TruncatedSVD và cosine similarity. (Phương thức: `item_based_svd`)
* **Lọc Cộng tác ALS (User-based):** Sử dụng mô hình Alternating Least Squares (ALS) được huấn luyện trước. Hỗ trợ gợi ý cho người dùng mới (chưa có trong tập huấn luyện) nếu họ có tương tác. (Phương thức: `als_user`)
* **Lọc Cộng tác ALS (Item-based):** Tìm sách tương tự dựa trên mô hình ALS. (Phương thức: `als_item`)
* **Lọc Dựa trên Nội dung (Content-based):** Gợi ý sách dựa trên sự tương đồng về mô tả và tác giả, sử dụng TF-IDF và cosine similarity. (Phương thức: `content_based`)
* **Phương thức Kết hợp (Hybrid):** Kết hợp điểm từ User-based SVD, Item-based SVD và Content-based với các trọng số `alpha`, `beta`, `gamma`. (Phương thức: `hybrid`)
* **Xử lý "Khởi đầu lạnh" (Cold Start):** Tự động đề xuất sách phổ biến nếu người dùng mới hoặc phương thức được chọn không đưa ra kết quả.

### 2. API Backend (FastAPI) - `api/main.py`
* **Endpoint Đề xuất Linh hoạt (`/recommend`):**
    * Hỗ trợ các phương thức: `user_based_svd`, `item_based_svd`, `content_based`, `als_user`, `als_item`, `hybrid`.
    * Tham số: `user_id`, `n_items`, `method`, `alpha` (cho hybrid), `beta` (cho hybrid), `gamma` (cho hybrid).
* **Endpoints Sản phẩm:**
    * `/products/list`: Trả về danh sách ID và tên tất cả sản phẩm.
    * `/products/{product_id}`: Trả về thông tin chi tiết của một sản phẩm.
* **Cung cấp Số liệu (Metrics - Ví dụ):** `/metrics/user-activity`, `/metrics/product-activity`.
* **Tự động Cập nhật Dữ liệu:** Dữ liệu (bao gồm cả mô hình SVD và tải mô hình ALS) được làm mới định kỳ (mặc định 1 phút) dựa trên thay đổi trong cơ sở dữ liệu.
* **Endpoint Làm mới Thủ công (`/refresh`):** Kích hoạt quá trình làm mới dữ liệu.

### 3. Dashboard Quản trị (Dash & Plotly) - `dashboard/app.py`
* **Trực quan hóa Gợi ý:**
    * Chọn User ID, Phương thức gợi ý, Số lượng gợi ý.
    * Hiển thị biểu đồ cột top sách được gợi ý.
* **Tra cứu Thông tin Sản phẩm:**
    * Dropdown chọn sản phẩm từ danh sách được tải từ API.
    * Hiển thị chi tiết sản phẩm được chọn (tên, tác giả, mô tả, v.v.).
* Tự động cập nhật danh sách sản phẩm trên dashboard.

## II. Công nghệ sử dụng
* **Python:** 3.8+
* **Backend & API:** FastAPI, Uvicorn
* **Machine Learning:** Scikit-learn (TruncatedSVD, TfidfVectorizer, cosine_similarity), Implicit (ALS)
* **Data Handling:** Pandas, NumPy
* **Database:** MySQL (kết nối qua PyMySQL, SQLAlchemy)
* **Scheduling:** APScheduler
* **Dashboard:** Dash, Plotly
* **Environment Management:** python-dotenv

## III. Cấu trúc thư mục dự án (Đề xuất)
your_project_root/
├── api/
│   └── main.py             # FastAPI application
├── data/
│   └── data_loader.py      # Functions to load data from DB
├── dashboard/
│   └── app.py              # Dash application
├── models/                 # Nơi lưu trữ mô hình đã huấn luyện (als_model_latest.pkl)
│   └── als_model_latest.pkl (được tạo bởi train_model.py)
├── scripts/
│   └── train_model.py      # Script để huấn luyện mô hình ALS
├── .env                    # File cấu hình môi trường (DB credentials, etc.)
├── requirements.txt        # Danh sách các thư viện Python
└── README.md               # Tài liệu này


## IV. Cài đặt và Chạy ứng dụng

1.  **Yêu cầu Tiên quyết:**
    * Python 3.8+ và Pip
    * Git
    * MySQL Server đang chạy

2.  **Thiết lập Môi trường:**
    ```bash
    git clone <your-repository-url>
    cd your_project_root
    python -m venv venv
    # Activate virtual environment
    # Windows:
    # venv\Scripts\activate
    # macOS/Linux:
    # source venv/bin/activate
    pip install -r requirements.txt
    ```

3.  **Cấu hình Cơ sở dữ liệu:**
    * Tạo database và các bảng như trong file `README.md` gốc của bạn (hoặc theo schema hiện tại của bạn).
    * Đảm bảo bảng `user_interaction` có cột `interaction_value` và `created_at`.
    * Đảm bảo bảng `products` có các cột `id`, `name`, `short_description`, `author`, và `created_at`.

4.  **Cấu hình Biến Môi trường:**
    * Tạo file `.env` trong thư mục gốc (`your_project_root`) từ file `.env.example` (nếu có) hoặc tạo mới.
    * Nội dung file `.env`:
        ```ini
        DB_USER=your_mysql_user
        DB_PASSWORD=your_mysql_password
        DB_SERVER=localhost # Hoặc IP/hostname của MySQL server
        DB_NAME=db_websitebansach
        DB_PORT=3306

        # Tùy chọn: đường dẫn lưu model
        MODEL_SAVE_DIR=./models 
        # MODEL_PATH=./models/als_model_latest.pkl # Hoặc để main.py tự ghép từ MODEL_SAVE_DIR

        # Tùy chọn: URL API cho dashboard (nếu chạy riêng)
        API_URL=[http://127.0.0.1:8000](http://127.0.0.1:8000) 
        ```

5.  **Huấn luyện Mô hình ALS (Lần đầu và định kỳ):**
    * Chạy script huấn luyện từ thư mục gốc:
        ```bash
        python -m scripts/train_model.py
        ```
    * Điều này sẽ tạo (hoặc cập nhật) file `als_model_latest.pkl` trong thư mục `models`. Chạy lại script này định kỳ (ví dụ, qua cron job) để cập nhật mô hình với dữ liệu mới.

6.  **Chạy API Backend:**
    * Từ thư mục gốc (`your_project_root`):
        ```bash
        uvicorn api.main:app --host 0.0.0.0 --port 8000 --reload
        ```
    * API sẽ có sẵn tại `http://localhost:8000`.
    * Tài liệu API (Swagger UI): `http://localhost:8000/docs`.

7.  **Chạy Dashboard:**
    * Từ thư mục gốc (`your_project_root`):
        ```bash
        python dashboard/app.py
        ```
    * Dashboard sẽ có sẵn tại `http://localhost:8050` (hoặc port bạn cấu hình trong `dashboard/app.py`).

## V. Sử dụng API
### 1. Lấy danh sách sản phẩm
```bash
curl -X POST "http://localhost:8000/recommend" \
-H "Content-Type: application/json" \
-d '{
  "user_id": 1,
  "n_items": 5,
  "method": "hybrid",
  "alpha": 0.4,
  "beta": 0.3,
  "gamma": 0.3
}'