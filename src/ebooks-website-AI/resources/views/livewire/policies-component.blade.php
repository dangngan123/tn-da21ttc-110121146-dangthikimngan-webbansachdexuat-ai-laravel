<div class="policy-container">
    <style>
        .policy-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Roboto', sans-serif;
        }

        .policy-nav {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-link {
            text-decoration: none;
            padding: 12px 18px;
            color: #495057;
            border-radius: 5px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
            font-size: 15px;
        }

        .nav-link:hover {
            background-color: #e9ecef;
            color: #212529;
        }

        .nav-link.active {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
        }

        .policy-section {
            display: none;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            line-height: 1.7;
        }

        .policy-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #0d6efd;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }

        h2 {
            font-size: 22px;
            margin: 25px 0 15px;
            color: #343a40;
        }

        h3 {
            font-size: 18px;
            margin: 20px 0 10px;
            color: #495057;
        }

        p {
            margin-bottom: 15px;
            color: #495057;
        }

        ul,
        ol {
            margin-bottom: 20px;
            padding-left: 25px;
        }

        li {
            margin-bottom: 8px;
        }

        .highlight {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }

        .contact-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .contact-info h3 {
            margin-top: 0;
            color: #343a40;
        }

        @media (max-width: 768px) {
            .nav-menu {
                flex-direction: column;
            }

            .policy-section {
                padding: 20px 15px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
    <nav class="policy-nav">
        <ul class="nav-menu">
            <li><a class="nav-link active" data-section="terms">Điều khoản sử dụng</a></li>
            <li><a class="nav-link" data-section="privacy">Bảo mật thông tin</a></li>
            <li><a class="nav-link" data-section="payment">Thanh toán</a></li>
            <li><a class="nav-link" data-section="return">Đổi trả sách</a></li>
            <li><a class="nav-link" data-section="shipping">Vận chuyển</a></li>
            <li><a class="nav-link" data-section="warranty">Bảo hành sách</a></li>
            <li><a class="nav-link" data-section="wholesale">Mua sỉ</a></li>
        </ul>
    </nav>

    <main class="policy-content">
        <div class="policy-sections">
            <section id="terms" class="policy-section active">
                <h1>Điều khoản sử dụng</h1>
                <div class="highlight">
                    <strong>Cập nhật lần cuối:</strong> Tháng 12, 2024
                </div>
                <h2>1. Chấp nhận điều khoản</h2>
                <p>Bằng việc truy cập và sử dụng website Panda Books, bạn đồng ý tuân thủ và bị ràng buộc bởi các điều khoản và điều kiện sử dụng được quy định dưới đây.</p>
                <h2>2. Định nghĩa</h2>
                <ul>
                    <li><strong>Website:</strong> Panda.com và tất cả các trang web liên quan</li>
                    <li><strong>Người dùng:</strong> Bất kỳ cá nhân hoặc tổ chức nào truy cập website</li>
                    <li><strong>Sản phẩm:</strong> Tất cả sách và văn phòng phẩm được bán trên website</li>
                    <li><strong>Dịch vụ:</strong> Các dịch vụ hỗ trợ khách hàng và giao hàng</li>
                </ul>
                <h2>3. Quyền và nghĩa vụ của người dùng</h2>
                <h3>3.1. Quyền của người dùng</h3>
                <ul>
                    <li>Được cung cấp thông tin chính xác về sách và sản phẩm</li>
                    <li>Được bảo mật thông tin cá nhân</li>
                    <li>Được hỗ trợ kỹ thuật và tư vấn sách</li>
                    <li>Được đổi trả sách theo chính sách</li>
                </ul>
                <h3>3.2. Nghĩa vụ của người dùng</h3>
                <ul>
                    <li>Cung cấp thông tin chính xác khi đăng ký</li>
                    <li>Không sử dụng website cho mục đích bất hợp pháp</li>
                    <li>Thanh toán đầy đủ cho đơn hàng sách</li>
                    <li>Tuân thủ các quy định về sử dụng</li>
                </ul>
                <h2>4. Quy định về tài khoản</h2>
                <p>Người dùng có trách nhiệm bảo mật thông tin tài khoản và mật khẩu. Mọi hoạt động dưới tài khoản của bạn sẽ được coi là do bạn thực hiện.</p>
                <h2>5. Chính sách nội dung</h2>
                <p>Nghiêm cấm đăng tải nội dung:</p>
                <ul>
                    <li>Vi phạm pháp luật Việt Nam</li>
                    <li>Xúc phạm danh dự, nhân phẩm</li>
                    <li>Spam hoặc quảng cáo không được phép</li>
                    <li>Chứa virus hoặc mã độc</li>
                </ul>
                <h2>6. Giới hạn trách nhiệm</h2>
                <p>Panda Books không chịu trách nhiệm cho các thiệt hại gián tiếp, ngẫu nhiên hoặc hậu quả phát sinh từ việc sử dụng website.</p>
                <div class="contact-info">
                    <h3>Thông tin liên hệ</h3>
                    <p><strong>Email:</strong> iamkimngan197@gmail.com</p>
                    <p><strong>Hotline:</strong>0795405536</p>
                    <p><strong>Địa chỉ:</strong>126 Nguyễn Thiện Thành, Phường 5, Trà Vinh</p>
                </div>
            </section>

            <section id="privacy" class="policy-section">
                <h1>Chính sách bảo mật thông tin cá nhân</h1>
                <div class="highlight">
                    <strong>Cam kết:</strong> Panda.com cam kết bảo vệ thông tin cá nhân của khách hàng theo tiêu chuẩn quốc tế.
                </div>
                <h2>1. Thông tin thu thập</h2>
                <h3>1.1. Thông tin cá nhân</h3>
                <ul>
                    <li>Họ tên, số điện thoại, email</li>
                    <li>Địa chỉ giao sách</li>
                    <li>Thông tin thanh toán (được mã hóa)</li>
                    <li>Lịch sử mua sách</li>
                </ul>
                <h3>1.2. Thông tin kỹ thuật</h3>
                <ul>
                    <li>Địa chỉ IP</li>
                    <li>Loại trình duyệt</li>
                    <li>Thời gian truy cập</li>
                    <li>Cookies và web beacons</li>
                </ul>
                <h2>2. Mục đích sử dụng thông tin</h2>
                <ul>
                    <li>Xử lý đơn hàng và giao sách</li>
                    <li>Hỗ trợ khách hàng</li>
                    <li>Gửi thông tin khuyến mãi sách mới (nếu đồng ý)</li>
                    <li>Cải thiện chất lượng dịch vụ</li>
                    <li>Phân tích hành vi đọc sách của người dùng</li>
                </ul>
                <h2>3. Bảo mật thông tin</h2>
                <h3>3.1. Biện pháp kỹ thuật</h3>
                <ul>
                    <li>Mã hóa SSL 256-bit</li>
                    <li>Firewall bảo mật</li>
                    <li>Hệ thống giám sát 24/7</li>
                    <li>Backup dữ liệu định kỳ</li>
                </ul>
                <h3>3.2. Biện pháp quản lý</h3>
                <ul>
                    <li>Giới hạn nhân viên truy cập dữ liệu</li>
                    <li>Đào tạo nhân viên về bảo mật</li>
                    <li>Kiểm tra định kỳ hệ thống</li>
                </ul>
                <div class="contact-info">
                    <h3>Thông tin liên hệ về bảo mật</h3>
                    <p><strong>Email:</strong> iamkimngan197@gmail.com</p>
                    <p><strong>Hotline:</strong> 0795405536</p>
                </div>
            </section>

            <section id="payment" class="policy-section">
                <h1>Chính sách thanh toán</h1>
                <div class="highlight">
                    <strong>Lưu ý:</strong> Panda.com cung cấp nhiều phương thức thanh toán an toàn và tiện lợi.
                </div>
                <h2>1. Phương thức thanh toán</h2>
                <h3>1.1. Thanh toán trực tuyến</h3>
                <ul>
                    <li>Thẻ tín dụng/ghi nợ (Visa, Mastercard, JCB)</li>
                    <!-- <li>Ví điện tử (Momo, ZaloPay, VNPay)</li> -->
                    <li>Chuyển khoản ngân hàng</li>
                    <li>Trả góp qua thẻ tín dụng (đơn hàng từ 3 triệu đồng)</li>
                </ul>
                <h3>1.2. Thanh toán khi nhận hàng (COD)</h3>
                <p>Áp dụng cho đơn hàng dưới 5 triệu đồng và trong phạm vi giao hàng của chúng tôi.</p>

                <h2>2. Quy trình thanh toán</h2>
                <ol>
                    <li>Chọn sách và thêm vào giỏ hàng</li>
                    <li>Tiến hành thanh toán và chọn phương thức</li>
                    <li>Điền thông tin thanh toán</li>
                    <li>Xác nhận đơn hàng</li>
                    <li>Nhận email xác nhận</li>
                </ol>

                <h2>3. Bảo mật thanh toán</h2>
                <p>Tất cả thông tin thanh toán được mã hóa theo tiêu chuẩn PCI DSS. Chúng tôi không lưu trữ thông tin thẻ tín dụng của khách hàng.</p>

                <h2>4. Hóa đơn điện tử</h2>
                <p>Hóa đơn điện tử sẽ được gửi qua email sau khi đơn hàng được xác nhận thanh toán thành công.</p>

                <div class="contact-info">
                    <h3>Hỗ trợ thanh toán</h3>
                    <p><strong>Email:</strong> iamkimngan197@gmail.com</p>
                    <p><strong>Hotline:</strong> 0795405536</p>
                </div>
            </section>

            <section id="return" class="policy-section">
                <h1>Chính sách đổi trả sách</h1>
                <div class="highlight">
                    <strong>Thời hạn đổi trả:</strong> 7 ngày kể từ ngày nhận sách
                </div>
                <h2>1. Điều kiện đổi trả</h2>
                <h3>1.1. Đổi trả do lỗi từ nhà sách</h3>
                <ul>
                    <li>Sách bị lỗi in ấn, thiếu trang</li>
                    <li>Sách bị hư hỏng trong quá trình vận chuyển</li>
                    <li>Giao sai sách so với đơn hàng</li>
                    <li>Sách không đúng mô tả trên website</li>
                </ul>
                <h3>1.2. Đổi trả theo nhu cầu khách hàng</h3>
                <ul>
                    <li>Sách còn nguyên vẹn, không có dấu hiệu sử dụng</li>
                    <li>Còn đầy đủ bao bì, tem nhãn</li>
                    <li>Có hóa đơn mua hàng</li>
                </ul>

                <h2>2. Quy trình đổi trả</h2>
                <ol>
                    <li>Liên hệ với bộ phận CSKH qua hotline hoặc email</li>
                    <li>Cung cấp thông tin đơn hàng và lý do đổi trả</li>
                    <li>Nhận mã đổi trả và hướng dẫn đóng gói</li>
                    <li>Gửi sách về địa chỉ được cung cấp</li>
                    <li>Nhận sách mới hoặc hoàn tiền trong vòng 7 ngày làm việc</li>
                </ol>

                <h2>3. Chi phí đổi trả</h2>
                <ul>
                    <li>Đổi trả do lỗi từ nhà sách: Miễn phí 100%</li>
                    <li>Đổi trả theo nhu cầu khách hàng: Khách hàng chịu phí vận chuyển hai chiều</li>
                </ul>

                <h2>4. Hình thức hoàn tiền</h2>
                <ul>
                    <li>Hoàn tiền vào tài khoản ngân hàng</li>
                    <li>Hoàn tiền vào ví điện tử</li>
                    <li>Hoàn tiền vào thẻ tín dụng (thời gian xử lý 7-15 ngày)</li>
                </ul>

                <div class="contact-info">
                    <h3>Bộ phận đổi trả</h3>
                    <p><strong>Email:</strong> iamkimngan197@gmail.com</p>
                    <p><strong>Hotline:</strong> 0795405536</p>
                </div>
            </section>

            <section id="shipping" class="policy-section">
                <h1>Chính sách vận chuyển</h1>
                <div class="highlight">
                    <strong>Cam kết:</strong> Giao sách nhanh chóng, an toàn đến tay người đọc
                </div>
                <h2>1. Phạm vi giao hàng</h2>
                <p>Panda Books giao hàng trên toàn quốc và quốc tế (một số quốc gia được chọn).</p>

                <h2>2. Thời gian giao hàng</h2>
                <h3>2.1. Nội thành Hà Nội và TP.HCM</h3>
                <ul>
                    <li>Giao hàng nhanh: 2-4 giờ (áp dụng cho đơn hàng trước 15h)</li>
                    <li>Giao hàng tiêu chuẩn: 1-2 ngày làm việc</li>
                </ul>
                <h3>2.2. Các tỉnh thành khác</h3>
                <ul>
                    <li>Thành phố lớn: 2-3 ngày làm việc</li>
                    <li>Vùng xa: 3-5 ngày làm việc</li>
                </ul>
                <h3>2.3. Quốc tế</h3>
                <ul>
                    <li>Châu Á: 5-7 ngày làm việc</li>
                    <li>Các khu vực khác: 7-14 ngày làm việc</li>
                </ul>

                <h2>3. Phí vận chuyển</h2>
                <h3>3.1. Trong nước</h3>
                <ul>
                    <li>Miễn phí cho đơn hàng từ 200.000đ</li>
                    <li>Đơn hàng dưới 300.000đ: 20.000đ - 40.000đ tùy khu vực</li>
                </ul>
                <h3>3.2. Quốc tế</h3>
                <p>Phí vận chuyển quốc tế được tính dựa trên trọng lượng và quốc gia nhận hàng.</p>

                <h2>4. Theo dõi đơn hàng</h2>
                <p>Khách hàng có thể theo dõi tình trạng đơn hàng thông qua:</p>
                <ul>
                    <li>Website: Đăng nhập tài khoản</li>
                    <li>App mobile: Mục "Đơn hàng của tôi"</li>
                    <li>SMS: Nhận thông báo tự động</li>
                    <li>Email: Cập nhật trạng thái</li>
                    <li>Hotline: Gọi 1900-xxxx</li>
                </ul>
                <h3>6.2. Trạng thái đơn hàng</h3>
                <ul>
                    <li><strong>Đã xác nhận:</strong> Đơn hàng được xác nhận</li>
                    <li><strong>Đang chuẩn bị:</strong> Đóng gói sản phẩm</li>
                    <li><strong>Đã xuất kho:</strong> Chuyển cho vận chuyển</li>
                    <li><strong>Đang giao:</strong> Shipper đang giao hàng</li>
                    <li><strong>Đã giao:</strong> Khách hàng đã nhận hàng</li>
                </ul>
            </section>

            <section id="wholesale" class="policy-section">
                <h1>Chính sách khách sỉ</h1>
                <div class="highlight">
                    <strong>Ưu đãi đặc biệt:</strong> Giảm giá đến 30% cho khách sỉ, hỗ trợ kinh doanh toàn diện.
                </div>
                <h2>1. Điều kiện trở thành khách sỉ</h2>
                <h3>1.1. Khách sỉ cá nhân</h3>
                <ul>
                    <li>Mua tối thiểu 10 sản phẩm cùng loại</li>
                    <li>Giá trị đơn hàng từ 5 triệu đồng</li>
                    <li>Có giấy tờ tùy thân hợp lệ</li>
                    <li>Cam kết mua hàng định kỳ</li>
                </ul>
                <h3>1.2. Khách sỉ doanh nghiệp</h3>
                <ul>
                    <li>Có giấy phép kinh doanh</li>
                    <li>Mã số thuế hợp lệ</li>
                    <li>Địa chỉ kinh doanh cố định</li>
                    <li>Doanh thu tối thiểu 50 triệu/năm</li>
                </ul>
                <h2>2. Phân loại khách sỉ</h2>
                <table>
                    <tr>
                        <th>Hạng</th>
                        <th>Điều kiện</th>
                        <th>Chiết khấu</th>
                        <th>Ưu đãi</th>
                    </tr>
                    <tr>
                        <td>Đồng</td>
                        <td>5-20 triệu/tháng</td>
                        <td>5-10%</td>
                        <td>Freeship, hỗ trợ marketing</td>
                    </tr>
                    <tr>
                        <td>Bạc</td>
                        <td>20-50 triệu/tháng</td>
                        <td>10-15%</td>
                        <td>Ưu tiên giao hàng, tư vấn</td>
                    </tr>
                    <tr>
                        <td>Vàng</td>
                        <td>50-100 triệu/tháng</td>
                        <td>15-20%</td>
                        <td>Sản phẩm độc quyền</td>
                    </tr>
                    <tr>
                        <td>Kim cương</td>
                        <td>Trên 100 triệu/tháng</td>
                        <td>20-30%</td>
                        <td>Đại lý độc quyền khu vực</td>
                    </tr>
                </table>
                <h2>3. Chính sách giá sỉ</h2>
                <h3>3.1. Cách tính giá</h3>
                <ul>
                    <li>Giá sỉ = Giá lẻ × (100% - % chiết khấu)</li>
                    <li>Chiết khấu theo số lượng và hạng khách hàng</li>
                    <li>Giá đặc biệt cho sản phẩm mới</li>
                    <li>Ưu đãi thêm cho đơn hàng lớn</li>
                </ul>
                <h3>3.2. Bảng chiết khấu theo số lượng</h3>
                <table>
                    <tr>
                        <th>Số lượng</th>
                        <th>Chiết khấu cơ bản</th>
                        <th>Chiết khấu VIP</th>
                    </tr>
                    <tr>
                        <td>10-50 sản phẩm</td>
                        <td>5%</td>
                        <td>8%</td>
                    </tr>
                    <tr>
                        <td>51-100 sản phẩm</td>
                        <td>10%</td>
                        <td>15%</td>
                    </tr>
                    <tr>
                        <td>101-500 sản phẩm</td>
                        <td>15%</td>
                        <td>20%</td>
                    </tr>
                    <tr>
                        <td>Trên 500 sản phẩm</td>
                        <td>20%</td>
                        <td>30%</td>
                    </tr>
                </table>
                <h2>4. Điều kiện thanh toán</h2>
                <h3>4.1. Khách sỉ mới</h3>
                <ul>
                    <li>Thanh toán 100% trước khi giao hàng</li>
                    <li>Chuyển khoản hoặc tiền mặt</li>
                    <li>Không áp dụng COD cho đơn hàng lớn</li>
                    <li>Có thể đặt cọc 50% cho đơn hàng đặc biệt</li>
                </ul>
                <h3>4.2. Khách sỉ thân thiết</h3>
                <ul>
                    <li>Được công nợ 15-30 ngày</li>
                    <li>Hạn mức tín dụng theo hạng khách hàng</li>
                    <li>Thanh toán định kỳ hàng tháng</li>
                    <li>Ưu đãi lãi suất cho thanh toán sớm</li>
                </ul>
                <h2>5. Hỗ trợ kinh doanh</h2>
                <h3>5.1. Marketing và quảng cáo</h3>
                <ul>
                    <li>Cung cấp hình ảnh sản phẩm chất lượng cao</li>
                    <li>Mẫu banner, poster quảng cáo</li>
                    <li>Hỗ trợ chạy ads Facebook, Google</li>
                    <li>Chia sẻ kinh nghiệm bán hàng</li>
                </ul>
                <h3>5.2. Đào tạo và tư vấn</h3>
                <ul>
                    <li>Đào tạo kiến thức sản phẩm</li>
                    <li>Kỹ năng bán hàng và chăm sóc khách hàng</li>
                    <li>Tư vấn chiến lược kinh doanh</li>
                    <li>Hỗ trợ kỹ thuật 24/7</li>
                </ul>
                <h3>5.3. Chính sách bảo vệ</h3>
                <ul>
                    <li>Không bán cho đại lý khác trong khu vực</li>
                    <li>Giá bán đề xuất để tránh cạnh tranh giá</li>
                    <li>Hỗ trợ xử lý khiếu nại từ khách hàng cuối</li>
                    <li>Chính sách đổi trả linh hoạt</li>
                </ul>
                <h2>6. Quy trình đăng ký</h2>
                <ol>
                    <li><strong>Liên hệ:</strong> Gọi hotline hoặc email đăng ký</li>
                    <li><strong>Tư vấn:</strong> Nhân viên tư vấn chính sách phù hợp</li>
                    <li><strong>Hồ sơ:</strong> Cung cấp giấy tờ cần thiết</li>
                    <li><strong>Thẩm định:</strong> Xét duyệt hồ sơ trong 3-5 ngày</li>
                    <li><strong>Ký hợp đồng:</strong> Ký hợp đồng hợp tác</li>
                    <li><strong>Kích hoạt:</strong> Tài khoản sỉ được kích hoạt</li>
                </ol>
                <h2>7. Chương trình ưu đãi</h2>
                <h3>7.1. Khuyến mãi theo mùa</h3>
                <ul>
                    <li>Tết Nguyên Đán: Giảm thêm 5-10%</li>
                    <li>Black Friday: Chiết khấu đặc biệt</li>
                    <li>Khai trương: Ưu đãi cho đại lý mới</li>
                    <li>Sinh nhật Panda.com: Quà tặng giá trị</li>
                </ul>
                <h3>7.2. Thưởng doanh số</h3>
                <ul>
                    <li>Đạt target tháng: Thưởng 2-5% doanh số</li>
                    <li>Top 10 đại lý: Du lịch nghỉ dưỡng</li>
                    <li>Tăng trưởng cao: Tăng hạng khách hàng</li>
                    <li>Giới thiệu đại lý mới: Hoa hồng 1%</li>
                </ul>
                <div class="contact-info">
                    <h3>Liên hệ kinh doanh sỉ</h3>
                    <p><strong>Hotline:</strong> 0795405536</p>
                    <p><strong>Email:</strong> iamkimngan197@gmail.com</p>
                    <p><strong>Zalo/WhatsApp:</strong> 0795405536</p>
                    <p><strong>Địa chỉ:</strong> 126 Nguyễn Thiện Thành, Phường 5, Trà Vinh</p>
                </div>
            </section>
        </div>
    </main>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.policy-section');

        // Hàm hiển thị section
        function showSection(sectionId) {
            sections.forEach(section => section.classList.remove('active'));
            navLinks.forEach(link => link.classList.remove('active'));

            const targetSection = document.getElementById(sectionId);
            const targetLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);

            if (targetSection && targetLink) {
                targetSection.classList.add('active');
                targetLink.classList.add('active');
            }
        }

        // Xử lý click trên menu
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-section');
                showSection(sectionId);
                // Cập nhật URL mà không tải lại trang
                history.pushState(null, '', `?section=${sectionId}`);
            });
        });

        // Kiểm tra query string khi tải trang
        const urlParams = new URLSearchParams(window.location.search);
        const sectionParam = urlParams.get('section');
        const validSections = ['terms', 'privacy', 'payment', 'return', 'warranty', 'shipping', 'wholesale'];
        if (sectionParam && validSections.includes(sectionParam)) {
            showSection(sectionParam);
        } else {
            showSection('terms'); // Mặc định hiển thị terms
        }
    });
</script>