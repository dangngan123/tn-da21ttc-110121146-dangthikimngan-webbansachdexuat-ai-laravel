# XÂY DỰNG WEBSITE BÁN SÁCH TRỰC TUYẾN TÍCH HỢP AI ĐỀ XUẤT SÁCH THEO SỞ THÍCH NGƯỜI DÙNG

## Thông tin dự án
  
**Giảng viên hướng dẫn**: ThS. Đoàn Phước Miền  
- **Email**: antonio86doan@gmail.com  
- **Số điện thoại**: 0978962954  

**Sinh viên thực hiện**: Đặng Thị Kim Ngân  
- **Email**: iamkimngan197@gmail.com  
- **Số điện thoại**: 0795405536  

## Giới thiệu

Dự án xây dựng một website bán sách trực tuyến với giao diện thân thiện, tích hợp các tính năng hiện đại như tìm kiếm, giỏ hàng, theo dõi đơn hàng và đặc biệt là hệ thống đề xuất sách cá nhân hóa dựa trên sở thích người dùng. Website không chỉ giúp người dùng dễ dàng mua sách mà còn mang đến trải nghiệm chọn sách thú vị thông qua gợi ý thông minh, tận dụng công nghệ AI.

### Lý do chọn đề tài

Trong thời đại công nghệ số, mua sách trực tuyến ngày càng phổ biến, nhưng việc chọn sách phù hợp với sở thích cá nhân vẫn là một thách thức. Dự án này ra đời nhằm giải quyết vấn đề đó bằng cách tích hợp AI để đề xuất sách dựa trên thói quen và hành vi của người dùng. Đây cũng là cơ hội để áp dụng kiến thức về phát triển web, AI và thiết kế hướng người dùng vào một sản phẩm thực tế.

### Mục tiêu

- Xây dựng website bán sách trực tuyến với giao diện dễ dùng, hỗ trợ tìm kiếm, mua hàng và thanh toán thuận tiện.  
- Tích hợp hệ thống đề xuất sách cá nhân hóa dựa trên lịch sử tương tác của người dùng.  
- Đảm bảo trải nghiệm người dùng mượt mà và tăng tính tương tác thông qua các tính năng như chatbot hỗ trợ và quản lý đánh giá.

## Chức năng chính

### Đối với người dùng
- **Giỏ hàng**: Thêm, cập nhật, xóa sách; hiển thị tổng chi phí.  
- **Đặt hàng**: Gửi đơn hàng với thông tin sản phẩm, địa chỉ và thanh toán.  
- **Tìm kiếm và lọc sách**: Theo tên, tác giả, danh mục, giá, độ tuổi.  
- **Đánh giá sản phẩm**: Đánh giá bằng sao và bình luận sau khi mua.  
- **Lịch sử đơn hàng**: Theo dõi trạng thái đơn hàng và chi tiết giao dịch.  
- **Danh sách yêu thích**: Lưu sách quan tâm.  
- **Khuyến mãi**: Áp dụng mã giảm giá hoặc tham gia ưu đãi.  
- **Hỗ trợ người dùng (chatbot)**: Trả lời câu hỏi về sản phẩm, đơn hàng, thanh toán, đổi trả.  
- **Gợi ý sách**: Đề xuất sách cá nhân hóa dựa trên lịch sử xem, mua và hành vi tương tác.  

### Đối với quản trị
- **Quản lý danh mục và sản phẩm**: Thêm, sửa, xóa sách.  
- **Quản lý đánh giá**: Kiểm duyệt bình luận của người dùng.  
- **Quản lý đơn hàng**: Theo dõi, xử lý và cập nhật trạng thái đơn hàng.  
- **Quản lý người dùng**: Kiểm soát thông tin và xử lý tài khoản vi phạm.  
- **Quản lý slides**: Tùy chỉnh hình ảnh trình chiếu trên trang chủ.  
- **Quản lý liên hệ**: Phản hồi câu hỏi, góp ý từ người dùng.  
- **Quản lý khuyến mãi**: Cập nhật chương trình ưu đãi và điều kiện áp dụng.  
- **Quản lý chatbot**: Thống kê số lượng sử dụng.  
- **Quản lý gợi ý sách**: Giám sát và điều chỉnh thuật toán gợi ý.  
- **Thống kê**: Báo cáo doanh thu, sản phẩm bán chạy và dữ liệu phân tích.

## Yêu cầu hệ thống

Để chạy dự án, hệ thống cần đáp ứng các yêu cầu sau:
- **PHP**: 8.2  
- **Laravel**: 11  
- **Livewire**: 3.0  
- **Python**: 3.13.3  
- **Composer**  
- **MySQL**  
- **Git**

## Hướng dẫn cài đặt

### 1. Tải dự án
```bash
git clone https://github.com/dangngan123/tn-da21ttc-110121146-dangthikimngan-webbansachdexuat-ai-laravel.git
cd tn-da21ttc-110121146-dangthikimngan-webbansachdexuat-ai-laravel
```

### 2. Cài đặt các gói phụ thuộc
```bash
composer install
npm install
```

### 3. Cấu hình môi trường
- Sao chép file `.env.example` thành `.env`:
```bash
cp .env.example .env
```
- Cập nhật các thông tin trong file `.env`, bao gồm:
  - Kết nối cơ sở dữ liệu MySQL
  - Cấu hình Google Login:
    ```env
    GOOGLE_CLIENT_ID=your-client-id
    GOOGLE_CLIENT_SECRET=your-client-secret
    GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
    ```

### 4. Tạo khóa ứng dụng
```bash
php artisan key:generate
```

### 5. Chạy migration và seed dữ liệu
```bash
php artisan migrate --seed
```

### 6. Khởi động server
```bash
php artisan serve
```
Truy cập website tại: `http://localhost:8000`

### 7. Cài đặt Python (nếu sử dụng AI đề xuất sách)
- Cài đặt các thư viện Python cần thiết:
```bash
pip install -r requirements.txt
```
- Chạy API Backend:
```bash
uvicorn api.main:app --reload --port 8001
```

## Lưu ý
- Đảm bảo đã cấu hình đúng Google Client ID và Secret để sử dụng tính năng đăng nhập bằng Google.  
- Kiểm tra kết nối MySQL trước khi chạy migration.  
- Nếu gặp lỗi, kiểm tra log trong `storage/logs/laravel.log`.

## Đóng góp
Mọi ý kiến đóng góp hoặc báo lỗi, vui lòng liên hệ qua email: **iamkimngan197@gmail.com**.
