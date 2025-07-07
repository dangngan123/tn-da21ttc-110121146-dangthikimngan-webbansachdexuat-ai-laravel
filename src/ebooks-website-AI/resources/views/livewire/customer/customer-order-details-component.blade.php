<div>
    <style>
        .container {
            margin-top: 20px;
        }

        .card {
            width: 80%;
            margin: 0 auto;
        }

        .table {
            width: 100%;
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            padding: 8px;
        }

        .table-sm th,
        .table-sm td {
            padding: 4px 8px;
        }

        /* Nút quay lại */
        .btn-back {
            background-color: #F15412;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            background-color: rgb(238, 232, 231);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-back i {
            font-size: 18px;
        }

        /* Khung mỗi đơn hàng */
        .order-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .order-card-header {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .order-card-body {
            padding: 10px;
        }
    </style>

    <div class="container">
        <!-- Nút quay lại với icon -->
        <a href="{{ route('customer.orders') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>

        <div class="card g-1">
            <div class="card-header">
                <h5 class="mb-0">Chi tiết đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <!-- Khung cho mỗi đơn hàng -->
                    @foreach($orders as $order)
                    <div class="order-card">
                        <div class="order-card-header">
                            <strong>Đơn hàng #{{$order->id}}</strong>
                        </div>
                        <div class="order-card-body">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Tên sản phẩm</th>
                                        <th>Ảnh</th>
                                        <th>Giá sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Giảm giá</th>
                                        <th>Chi phí vận chuyển</th>
                                        <th>Ngày đặt hàng</th>
                                        <th style="color:  #F15412;">Tổng số tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $ordersItem)
                                    <tr class="text-center">
                                        <td>{{$ordersItem->product->name}}</td>
                                        <td><a href="{{$ordersItem->product->image}}" data-lightbox="example-1">
                                                <img src="{{ $ordersItem->product->image }}" alt="Slider Image" style="width:80px"></a>
                                        </td>
                                        <td>{{$ordersItem->price}}đ</td>
                                        <td>x {{$ordersItem->quantity}}</td>
                                        <td>{{$order->discount ?? '0'}}đ</td>
                                        <td>{{$ordersItem->order->shipping_cost}}đ</td>
                                        <td>{{$ordersItem->created_at}}</td>
                                        <td style="color:  #F15412;">{{$ordersItem->order->total}}đ</td>
                                        <td>
                                            @if($ordersItem->review && $ordersItem->review->status == 'pending')
                                            <a href="{{ route('customer.review', $ordersItem->id) }}" class="btn-small d-block" style="color:rgb(177, 6, 6); white-space: nowrap;">
                                                Xem đánh giá
                                            </a>
                                            @else
                                            <a href="{{ route('customer.review', $ordersItem->id) }}" class="btn-small d-block" style="color:rgb(177, 6, 6); white-space: nowrap;">
                                                Đánh giá
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>