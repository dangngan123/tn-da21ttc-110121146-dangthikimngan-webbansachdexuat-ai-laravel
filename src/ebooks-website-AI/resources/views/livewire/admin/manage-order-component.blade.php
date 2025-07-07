<div>
    <style>
        .stats-card {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            margin-right: 10px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-card .badge {
            margin-right: 8px;
            font-size: 12px;
        }

        .stats-card span {
            font-weight: bold;
            color: #333;
        }

        .badge.bg-primary { background-color: #007bff; }
        .badge.bg-warning { background-color: #ffc107; }
        .badge.bg-info { background-color: #17a2b8; }
        .badge.bg-success { background-color: #28a745; }
        .badge.bg-danger { background-color: #dc3545; }

        /* Style cho badge trong bảng */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            color: #fff;
            text-align: center;
            min-width: 80px;
            margin-bottom: 5px;
        }

        /* Style cho select */
        .status-select {
            border-radius: 5px;
            font-size: 14px;
            padding: 5px;
            width: 100%;
            border: 1px solid #ced4da;
        }

        .status-select:disabled {
            background-color: #e9ecef;
            opacity: 1;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="shop-product-fillter mb-0">
                <!-- Phần thống kê đơn hàng -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="stats-card">
                        <span class="badge bg-primary text-white">Đã đặt hàng</span>
                        <span>{{ $orderedOrders }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-warning text-white">Chờ xử lý</span>
                        <span>{{ $pendingOrders }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-info text-white">Đang vận chuyển</span>
                        <span>{{ $shippingOrders }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-success text-white">Đã giao hàng</span>
                        <span>{{ $completedOrders }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-danger text-white">Đã hủy</span>
                        <span>{{ $canceledOrders }}</span>
                    </div>
                </div>
                <div class="sort-by-product-area align-items-center">
                    <div class="totall-product">
                        <div class="sidebar-widget widget_search" style="background-color: rgb(255, 255, 255);">
                            <div class="search-form">
                                <form action="#">
                                    <input type="text" placeholder="Tìm kiếm…" wire:model.live="search" style="width: 300px;">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="me-3" style="font-size: 14px; line-height: 1.5;">
                            <div style="position: relative;">
                                <select class="form-control" wire:model.live="statusFilter" style="border-radius: 5px; background-color: #f1f1f1; color: #333; font-size: 14px; padding-left: 30px;">
                                    <option value="">Tất cả</option>
                                    <option value="ordered">Đã đặt hàng</option>
                                    <option value="processing">Đang xử lý</option>
                                    <option value="shipped">Đang vận chuyển</option>
                                    <option value="delivered">Đã giao hàng</option>
                                    <option value="canceled">Đã bị hủy</option>
                                </select>
                                <i class="fa-solid fa-filter" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #007bff;"></i>
                            </div>
                        </label>
                    </div>
                    <div class="sort-by-cover mr-10">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps"></i>Đã chọn:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> {{ count($selectedItems) }} <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul class="menu">
                                <li><a href="#" wire:click.prevent="selectedDelete"><i class="fi-rs-trash"></i> Xóa</a></li>
                                <li><a href="#" wire:click.prevent="export"><i class="fi-rs-download mr-5"></i>Export</a></li>
                            </ul>
                        </div>
                    </div>

                    

                    <div class="sort-by-cover">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps-sort"></i>Hiển thị:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> {{$pagesize}} <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul>
                                <li><a class="{{ $pagesize == 12 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(12)">12</a></li>
                                <li><a class="{{ $pagesize == 24 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(24)">24</a></li>
                                <li><a class="{{ $pagesize == 36 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(36)">36</a></li>
                                <li><a class="{{ $pagesize == 48 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(48)">48</a></li>
                                <li><a class="{{ $pagesize == 50 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(50)">50</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th><input type="checkbox" wire:model.live="selectAll" class="small-checkbox"></th>
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th style="color: #F15412;">Tổng số tiền</th>
                            <th>Ngày đặt hàng</th>
                            <th>Ghi chú</th>
                            <th>Giao dịch</th>
                            <th>Tình trạng</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $index => $order)
                        <tr class="{{ $this->isColor($order->id) }} text-center" wire:key="order-{{ $order->id }}">
                            <td class="small-checkbox">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $order->id }}">
                            </td>
                            <td>{{ $index + $orders->firstItem() }}</td>
                            <td>{{ $order->order_code }}</td>
                            <td style="color: #F15412;">{{ number_format($order->total, 3, ',', '.') }}đ</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $order->additional_info }}</td>
                            <td>
                                <span class="badge payment-type-{{ $order->transaction->payment_type ?? 'unknown' }}" style="color:black;">
                                    {{ $order->transaction->payment_type === 'cod' ? 'COD' : ($order->transaction->payment_type === 'payos' ? 'PayOS' : ($order->transaction->payment_type === 'vnpay' ? 'VNPay' : 'Không xác định')) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge {{ $order->status === 'ordered' ? 'bg-primary' : ($order->status === 'processing' ? 'bg-warning' : ($order->status === 'shipped' ? 'bg-info' : ($order->status === 'delivered' ? 'bg-success' : ($order->status === 'canceled' ? 'bg-danger' : '')))) }}">
                                    @switch($order->status)
                                        @case('ordered') Đã đặt hàng @break
                                        @case('processing') Đang xử lý @break
                                        @case('shipped') Đang vận chuyển @break
                                        @case('delivered') Đã giao hàng @break
                                        @case('canceled') Đã bị hủy @break
                                        @default Không xác định
                                    @endswitch
                                </span>
                                <select class="form-control status-select" wire:model.live="orderStatus.{{ $order->id }}"
                                        wire:change="handleStatusChange({{ $order->id }}, $event.target.value)"
                                        {{ in_array($order->status, ['canceled', 'delivered']) ? 'disabled' : '' }}>
                                    <option value="ordered" {{ $order->status === 'ordered' ? 'selected' : '' }}>Đã đặt hàng</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Đang vận chuyển</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                    <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>Đã bị hủy</option>
                                </select>
                            </td>
                            <td>
                                <a href="javascript:void(0);" wire:click="showOrderDetails({{ $order->id }})" title="Chi tiết" class="btn-small d-block btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-danger">Không có đơn hàng nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <!-- Modal hiển thị chi tiết đơn hàng -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Chi tiết đơn hàng #ĐH{{ $selectedOrder['id'] ?? '' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($selectedOrder)
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="color: #007bff;">Thông tin khách hàng</h5>
                            <p><strong>Tên:</strong> {{ $selectedOrder['name'] }}</p>
                            <p><strong>Email:</strong> {{ $selectedOrder['email'] }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $selectedOrder['phone'] }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $selectedOrder['address'] }}, {{ $selectedOrder['ward'] }}, {{ $selectedOrder['district'] }}, {{ $selectedOrder['province'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 style="color: #007bff;">Thông tin đơn hàng</h5>
                            <p style="color:#F15412"><strong>Tổng tiền:</strong> {{ number_format($selectedOrder['total'], 3, ',', '.') }}đ</p>
                            <p><strong>Phương thức thanh toán:</strong>
                                {{ $selectedOrder['transaction']['payment_type'] === 'cod' ? 'COD' : ($selectedOrder['transaction']['payment_type'] === 'payos' ? 'PayOS' : ($selectedOrder['transaction']['payment_type'] === 'vnpay' ? 'VNPay' : 'Không xác định')) }}
                            </p>
                            <p><strong>Trạng thái:</strong>
                                <span class="status-badge {{ $selectedOrder['status'] === 'ordered' ? 'bg-primary' : ($selectedOrder['status'] === 'processing' ? 'bg-warning' : ($selectedOrder['status'] === 'shipped' ? 'bg-info' : ($selectedOrder['status'] === 'delivered' ? 'bg-success' : ($selectedOrder['status'] === 'canceled' ? 'bg-danger' : '')))) }}">
                                    @switch($selectedOrder['status'])
                                        @case('ordered') Đã đặt hàng @break
                                        @case('processing') Đang xử lý @break
                                        @case('shipped') Đang vận chuyển @break
                                        @case('delivered') Đã giao hàng @break
                                        @case('canceled') Đã bị hủy @break
                                        @default Không xác định
                                    @endswitch
                                </span>
                            </p>
                            <p><strong>Ngày đặt hàng:</strong> {{ \Carbon\Carbon::parse($selectedOrder['created_at'])->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <hr>
                    <h5 style="color: #007bff;">Sản phẩm trong đơn hàng</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedOrder['orderItems'] as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ number_format($item['price'], 3, ',', '.') }}₫</td>
                                <td>{{ number_format($item['price'] * $item['quantity'], 3, ',', '.') }}₫</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Không có sản phẩm nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @else
                    <p class="text-center">Không có thông tin chi tiết đơn hàng.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-order-details-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                modal.show();
            });

            document.getElementById('orderDetailsModal').addEventListener('hidden.bs.modal', () => {
                Livewire.find(Livewire.lastTouchedComponentId).set('selectedOrder', null);
            });

            Livewire.on('confirm-cancel', (orderId) => {
                if (confirm('Bạn chắc chắn hủy đơn hàng này chứ?')) {
                    Livewire.find(Livewire.lastTouchedComponentId).call('confirmCancel', orderId);
                } else {
                    Livewire.find(Livewire.lastTouchedComponentId).call('resetStatus', orderId);
                }
            });
        });
    </script>
    @endpush
</div>