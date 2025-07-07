<div>
    <style>
       

        .status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 16px;
            font-size: 14px;
        }

        .carrier {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .battery {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
        }

        .back-button {
            border: none;
            background: none;
            padding: 8px;
            cursor: pointer;
        }

        .title {
            font-size: 20px;
            font-weight: 500;
        }

        .help-button {
            color: #0066cc;
            border: none;
            background: none;
            cursor: pointer;
        }

        .nav-icons {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            padding: 16px 24px;
            gap: 10px;
            overflow-x: auto;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            position: relative;
            flex: 1;
            min-width: 0;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
            cursor: pointer;
        }

        .nav-icon {
            padding: 8px;
            transition: color 0.3s ease;
        }

        .nav-text {
            font-size: 14px;
            white-space: nowrap;
            transition: color 0.3s ease;
        }

        .nav-item:hover {
            background-color: #f5f5f5;
            color: #ff4444;
        }

        .nav-item:hover .nav-icon,
        .nav-item:hover .nav-text {
            color: #ff4444;
        }

        .nav-item.active {
            background-color: #ff4444;
            color: white;
        }

        .nav-item.active .nav-icon,
        .nav-item.active .nav-text {
            color: white;
        }

        .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ff4444;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }

        .nav-item.active .badge {
            background: white;
            color: #ff4444;
        }

        .review-card {
            margin: 16px;
            padding: 16px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .review-title {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .review-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .product-description {
            color: #666;
            font-size: 14px;
        }

        .review-button {
            background: #ff4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            cursor: pointer;
        }

        .order-card h5 {
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .btn-danger {
            font-size: 12px;
            padding: 5px 8px;
        }

        .btn-custom-small {
            font-size: 12px;
            padding: 5px 10px;
        }

        .btn.btn-sm {
            padding: 0.5rem 0.5rem;
            white-space: nowrap;
            font-size: 9px;
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-sm:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }

        #noOrdersMessage {
            display: none;
            text-align: center;
            padding: 50px 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .row {
            --bs-gutter-x: 0 !important;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background: #ff4444;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .nav-icon {
            position: relative;
        }

        .gap-2 {
            gap: 8px;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 8px;
            padding: 16px;
        }

        .modal-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: bold;
        }

        .modal-body {
            padding: 16px 0;
        }

        .modal-footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Prevent modal from opening when clicking buttons inside order-card */
        .order-card .btn {
            cursor: pointer;
        }
    </style>
    <main class="main">
        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="header">
                            <div>Đơn hàng của bạn</div>
                        </div>
                    </div>
                </div>

                <!-- Hiển thị thông báo flash -->
                @if(session('message'))
                <div class="alert alert-success mt-3 mb-0" role="alert">
                    {{ session('message') }}
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger mt-3 mb-0" role="alert">
                    {{ session('error') }}
                </div>
                @endif

                <div class="nav-icons">
                    <div class="nav-item" data-status="all">
                        <div class="nav-icon">
                            <i class="bi bi-collection"></i>
                            <span class="badge" id="badge-all">0</span>
                        </div>
                        <div class="nav-text">Tất cả</div>
                    </div>
                    <div class="nav-item" data-status="ordered">
                        <div class="nav-icon">
                            <i class="bi bi-cart"></i>
                            <span class="badge" id="badge-ordered">0</span>
                        </div>
                        <div class="nav-text">Đã đặt hàng</div>
                    </div>
                    <div class="nav-item" data-status="processing">
                        <div class="nav-icon">
                            <i class="bi bi-hourglass-split"></i>
                            <span class="badge" id="badge-processing">0</span>
                        </div>
                        <div class="nav-text">Đang xử lý</div>
                    </div>
                    <div class="nav-item" data-status="shipped">
                        <div class="nav-icon">
                            <i class="bi bi-truck"></i>
                            <span class="badge" id="badge-shipped">0</span>
                        </div>
                        <div class="nav-text">Đang vận chuyển</div>
                    </div>
                    <div class="nav-item" data-status="delivered">
                        <div class="nav-icon">
                            <i class="bi bi-check-circle"></i>
                            <span class="badge" id="badge-delivered">0</span>
                        </div>
                        <div class="nav-text">Đã giao hàng</div>
                    </div>
                    <div class="nav-item" data-status="canceled">
                        <div class="nav-icon">
                            <i class="bi bi-x-circle"></i>
                            <span class="badge" id="badge-canceled">0</span>
                        </div>
                        <div class="nav-text">Đã bị hủy</div>
                    </div>
                </div>

                <section class="mt-50 mb-50">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="orders-list" id="ordersList">
                                    <div id="noOrdersMessage" style="display: none; text-align: center; padding: 50px 0;">
                                        <p>Bạn chưa có đơn hàng nào.</p>
                                        <img src="{{ asset('assets/imgs/cart/ico_emptycart.svg') }}" alt="" width="150px">
                                    </div>

                                    @foreach($orderItems->groupBy('order_id') as $orderId => $items)
                                    <div class="order-card border rounded mb-3 p-3" data-status="{{ $items[0]->order->status }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>
                                                <p class="mb-0" style="color:rgb(6, 6, 6); font-size: 15px; font-weight: bold;">
                                                    #{{ $items[0]->order->order_code }}
                                                </p>
                                            </h5>

                                            <span class="text-muted">
                                                @php
                                                $status = $items[0]->order->status;
                                                @endphp

                                                @if($status == 'ordered')
                                                <span class="text-info">Đã đặt hàng</span>
                                                @elseif($status == 'processing')
                                                <span class="text-warning">Đang xử lý</span>
                                                @elseif($status == 'shipped')
                                                <span class="text-primary">Đang vận chuyển</span>
                                                @elseif($status == 'delivered')
                                                <span class="text-success">Đã giao hàng</span>
                                                @elseif($status == 'canceled')
                                                <span class="text-danger">Đã bị hủy</span>
                                                @else
                                                <span class="text-secondary">Không xác định</span>
                                                @endif
                                            </span>
                                        </div>

                                        @foreach($items as $ordersItem)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-2">
                                                <img src="{{ asset('admin/product/'.$ordersItem->product->image) }}" alt="Product Image" class="img-fluid" style="width: 50px; height: auto;">
                                            </div>
                                            <div class="col-6">
                                                <h6>{{ $ordersItem->product->name }}</h6>
                                                <p class="text-muted font-xs">{{ $ordersItem->product->author }}</p>
                                            </div>
                                            <div class="col-2 text-center">
                                                <p>x{{ $ordersItem->quantity }}</p>
                                            </div>
                                            <div class="col-2 text-end">
                                                <p>
                                                    <span style="text-decoration: line-through; margin-right: 5px;">
                                                        {{ number_format($ordersItem->product->reguler_price, 3, ',', '.') }}₫
                                                    </span>
                                                    {{ number_format($ordersItem->price, 3, ',', '.') }}₫
                                                </p>
                                            </div>
                                        </div>

                                        @if($ordersItem->order->status === 'delivered')
                                        @php
                                        $reviewed = \App\Models\Review::where('order_item_id', $ordersItem->id)->exists();
                                        @endphp

                                        <div class="text-end d-flex justify-content-end align-items-center gap-2">
                                            @if (!$reviewed)
                                            <a href="{{ route('customer.review', $ordersItem->id) }}" class="btn btn-sm btn-danger" onclick="event.stopPropagation();">Đánh giá</a>
                                            @else
                                            <button class="btn btn-sm btn-secondary" disabled onclick="event.stopPropagation();">Đã đánh giá</button>
                                            @endif

                                            <a href="{{ route('details', $ordersItem->product->slug) }}" class="btn btn-sm" style="background-color: white; color: black; border: 1px solid black;" onclick="event.stopPropagation();">Mua lại</a>
                                        </div>
                                        @elseif($ordersItem->order->status === 'ordered')
                                        <div class="text-end">
                                            <button type="button" class="btn btn-sm btn-danger" wire:click="cancelOrder({{ $ordersItem->order->id }})" onclick="event.stopPropagation();">Hủy đơn hàng</button>
                                        </div>
                                        @endif
                                        @endforeach

                                        <div class="cart-action text-end mt-3">
                                            @php
                                                $totalQuantity = $items->sum('quantity');
                                            @endphp
                                            <button class="btn btn-custom-small" style="background-color:#f04515; color: white;" onclick="event.stopPropagation();"
                                                data-bs-toggle="modal" data-bs-target="#orderDetailsModal_{{ $orderId }}">
                                                Tổng số tiền ({{ $totalQuantity }} sản phẩm): {{ number_format($items[0]->order->total, 3, ',', '.') }}₫
                                            </button>
                                        </div>
                                        <br>
                                    </div>

                                    <!-- Modal chi tiết đơn hàng -->
                                    <div class="modal fade" id="orderDetailsModal_{{ $orderId }}" tabindex="-1" aria-labelledby="orderDetailsModalLabel_{{ $orderId }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderDetailsModalLabel_{{ $orderId }}">Chi tiết đơn hàng #{{ $items[0]->order->order_code }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Trạng thái:</strong>
                                                        @if($items[0]->order->status == 'ordered')
                                                        <span class="text-info">Đã đặt hàng</span>
                                                        @elseif($items[0]->order->status == 'processing')
                                                        <span class="text-warning">Đang xử lý</span>
                                                        @elseif($items[0]->order->status == 'shipped')
                                                        <span class="text-primary">Đang vận chuyển</span>
                                                        @elseif($items[0]->order->status == 'delivered')
                                                        <span class="text-success">Đã giao hàng</span>
                                                        @elseif($items[0]->order->status == 'canceled')
                                                        <span class="text-danger">Đã bị hủy</span>
                                                        @else
                                                        <span class="text-secondary">Không xác định</span>
                                                        @endif
                                                    </p>
                                                    <p><strong>Ngày đặt hàng:</strong> {{ $items[0]->order->created_at->format('d/m/Y H:i') }}</p>
                                                    <p><strong>Địa chỉ giao hàng:</strong> {{ $items[0]->order->address }}, {{ $items[0]->order->ward }}, {{ $items[0]->order->district }}, {{ $items[0]->order->province }}</p>
                                                    <p><strong>Tên khách hàng:</strong> {{ $items[0]->order->name }}</p>
                                                    <p><strong>Email:</strong> {{ $items[0]->order->email ?? 'Không có' }}</p>
                                                    <p><strong>Số điện thoại:</strong> {{ $items[0]->order->phone }}</p>
                                                    <hr>
                                                    <h6>Sản phẩm:</h6>
                                                    @foreach($items as $item)
                                                    <div class="row align-items-center mb-2">
                                                        <div class="col-2">
                                                            <img src="{{ asset('admin/product/'.$item->product->image) }}" alt="Product Image" class="img-fluid" style="width: 50px; height: auto;">
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">{{ $item->product->name }}</p>
                                                            <small class="text-muted">{{ $item->product->author }}</small>
                                                        </div>
                                                        <div class="col-2 text-center">
                                                            <p class="mb-0">x{{ $item->quantity }}</p>
                                                        </div>
                                                        <div class="col-2 text-end">
                                                            <p class="mb-0">{{ number_format($item->price, 3, ',', '.') }}₫</p>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    <hr>
                                                    <p><strong>Tổng tiền hàng:</strong> {{ number_format($items[0]->order->subtotal, 3, ',', '.') }}₫</p>
                                                    @if($items[0]->order->discount > 0)
                                                    <p><strong>Giảm giá:</strong> - {{ number_format($items[0]->order->discount, 3, ',', '.') }}₫</p>
                                                    @endif
                                                    <p><strong>Chi phí vận chuyển:</strong>
                                                        @if($items[0]->order->shipping_cost == 0)
                                                            Miễn phí
                                                        @else
                                                            {{ number_format($items[0]->order->shipping_cost, 3, ',', '.') }}₫
                                                        @endif
                                                    </p>
                                                    <p><strong>Tổng thanh toán:</strong> {{ number_format($items[0]->order->total, 3, ',', '.') }}₫</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- JavaScript for Tab Navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.nav-item');
            const orders = document.querySelectorAll('.order-card');
            const noOrdersMessage = document.getElementById('noOrdersMessage');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const status = tab.getAttribute('data-status');

                    // Remove active class from all tabs
                    tabs.forEach(item => item.classList.remove('active'));
                    // Add active class to the clicked tab
                    tab.classList.add('active');

                    // Show or hide orders based on the selected tab
                    let visibleOrders = 0;
                    orders.forEach(order => {
                        if (status === 'all' || order.getAttribute('data-status') === status) {
                            order.style.display = 'block';
                            visibleOrders++;
                        } else {
                            order.style.display = 'none';
                        }
                    });

                    // Show or hide the "No Orders" message
                    if (visibleOrders === 0) {
                        noOrdersMessage.style.display = 'block';
                    } else {
                        noOrdersMessage.style.display = 'none';
                    }
                });
            });

            // Initialize badge counts
            const badges = {
                all: document.getElementById('badge-all'),
                ordered: document.getElementById('badge-ordered'),
                processing: document.getElementById('badge-processing'),
                shipped: document.getElementById('badge-shipped'),
                delivered: document.getElementById('badge-delivered'),
                canceled: document.getElementById('badge-canceled'),
            };

            const orderCounts = {
                all: orders.length,
                ordered: 0,
                processing: 0,
                shipped: 0,
                delivered: 0,
                canceled: 0,
            };

            orders.forEach(order => {
                const status = order.getAttribute('data-status');
                if (orderCounts[status] !== undefined) {
                    orderCounts[status]++;
                }
            });

            for (const [status, count] of Object.entries(orderCounts)) {
                if (badges[status]) {
                    badges[status].textContent = count;
                }
            }

            // Trigger the 'all' tab by default
            const allTab = document.querySelector('.nav-item[data-status="all"]');
            if (allTab) {
                allTab.click();
            }
        });
    </script>