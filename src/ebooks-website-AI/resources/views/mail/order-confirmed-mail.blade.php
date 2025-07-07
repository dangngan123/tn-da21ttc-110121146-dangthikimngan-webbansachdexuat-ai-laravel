<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo đơn hàng #{{ $order->order_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #F15412;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
        }

        .content p {
            margin: 10px 0;
        }

        .highlight {
            color: #F15412;
            font-weight: bold;
        }

        .order-details {
            margin: 20px 0;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .item-table th,
        .item-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .item-table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .item-table .item-image img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .order-totals {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .order-totals p {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .order-totals .highlight {
            font-size: 16px;
        }

        .thank-you {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #F15412;
            color: #ffffff;
            font-size: 14px;
        }

        .footer a {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Xác nhận đơn hàng</h1>
        </div>

        <!-- Nội dung chính -->
        <div class="content">
            <p>Xin chào <span class="highlight">{{ $order->name }}</span>,</p>
            <p>Đơn hàng <span class="highlight">#{{ $order->order_code }}</span> của bạn đã được đặt thành công vào ngày <span class="highlight">{{ $order->created_at->format('d/m/Y H:i') }}</span>.</p>

            <!-- Chi tiết đơn hàng -->
            <div class="order-details">
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td class="item-image">
                                <img src="{{ asset('admin/product/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                            </td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 3, ',', '.') }} VNĐ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Tổng tiền -->
                <div class="order-totals">
                    <p>
                        <span>Tổng tiền hàng: </span>
                        <span class="highlight">{{ number_format($order->subtotal, 3, ',', '.') }} VNĐ</span>
                    </p>
                    <p>
                        <span>Chi phí vận chuyển: </span>
                        <span class="highlight">{{ number_format($order->shipping_cost, 3, ',', '.') }} VNĐ</span>
                    </p>
                    <p>
                        <span>Tổng thanh toán: </span>
                        <span class="highlight">{{ number_format($order->total, 3, ',', '.') }} VNĐ</span>
                    </p>
                </div>
            </div>

            <!-- Lời cảm ơn -->
            <div class="thank-you">
                <p>Một lần nữa, chúng tôi xin chân thành cảm ơn quý khách đã tin tưởng và ủng hộ. Chúc quý khách một ngày tốt lành!</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© 2025 Panda.com - Nhà sách trực tuyến hàng đầu Việt Nam. Mọi quyền được bảo lưu.</p>
            <p>Liên hệ: <a href="mailto:iamkimngan197@gmail.com">iamkimngan197@gmail.com</a></p>
            <p>Địa chỉ: 126 Nguyễn Thiện Thành, Phường 5, Trà Vinh </p>
            <p>Điện thoại: <a href="tel:+84123456789">+84 795 405 536</a></p>
        </div>
    </div>
</body>

</html>