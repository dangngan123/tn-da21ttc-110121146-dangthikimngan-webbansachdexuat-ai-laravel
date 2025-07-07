<div>
    <style>
        .container {
            margin-top: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #F15412;
            color: white;
            padding: 16px;
            font-size: 1.25rem;
            font-weight: 600;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .table {
            width: 100%;
            font-size: 1rem;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #F9F9F9;
            color: #333;
            font-weight: 500;
        }

        .table td {
            background-color: #fff;
            color: #333;
        }

        .table img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .order-info {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .order-info h5 {
            margin-bottom: 15px;
            font-size: 1.25rem;
        }

        .order-info .order-detail {
            display: flex;
            justify-content: space-between;
            font-size: 1rem;
            padding: 8px 0;
        }

        .order-info .order-total {
            font-size: 1.25rem;
            font-weight: bold;
            color: #F15412;
        }

        .btn-back {
            background-color: #F15412;
            color: white;
            padding: 10px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .btn-back i {
            margin-right: 6px;
        }

        .btn-back:hover {
            background-color: #e44909;
        }
    </style>

    <div class="container">
        <a href="{{ route('admin.dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>

        <!-- Khung thông tin đơn hàng -->
        <div class="order-info">
            <h5>Thông tin đơn hàng</h5>
            <div class="order-detail">
                <strong>Mã đơn hàng:</strong>
                <span>#{{ $orderItems->first()->order->id }}</span>
            </div>
            <div class="order-detail">
                <strong>Tổng số tiền:</strong>
                <span class="order-total">{{ $orderItems->first()->order->total }}đ</span>
            </div>
        </div>

        <div class="card">
            <!-- <div class="card-header">
                <h5>Chi tiết sản phẩm</h5>
            </div> -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Ảnh</th>
                                <th>Giá sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giảm giá</th>
                                <th>Chi phí vận chuyển</th>
                                <th>Ngày đặt hàng</th>


                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderItems as $ordersItem)
                            <tr>
                                <td>{{ $ordersItem->product->name }}</td>
                                <td>
                                    <a href="{{ $ordersItem->product->image }}" data-lightbox="example-1">
                                        <img src="{{asset('admin/product/'.$ordersItem->product->image) }}" style="width:80px" alt="Slider Image">
                                    </a>
                                </td>
                                <td>{{ $ordersItem->price }}đ</td>
                                <td>x {{ $ordersItem->quantity }}</td>
                                <td>{{ $ordersItem->order->discount ?? '0' }}đ</td>
                                <td>{{ $ordersItem->order->shipping_cost ?? '0' }}đ</td>
                                <td>{{ $ordersItem->created_at }}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>