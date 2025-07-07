<div>
    <style>
        .radio-inputs {
            display: flex;
            justify-content: center;
            align-items: center;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .radio-inputs>* {
            margin: 6px;
        }

        .radio-input:checked+.radio-tile {
            border-color: #2260ff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            color: #2260ff;
        }

        .radio-input:checked+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
            background-color: #2260ff;
            border-color: #2260ff;
        }

        .radio-input:checked+.radio-tile .radio-icon svg {
            fill: #2260ff;
        }

        .radio-input:checked+.radio-tile .radio-label {
            color: #2260ff;
        }

        .radio-input:focus+.radio-tile {
            border-color: #2260ff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1), 0 0 0 4px #b5c9fc;
        }

        .radio-input:focus+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
        }

        .radio-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80px;
            min-height: 80px;
            border-radius: 0.5rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            transition: 0.15s ease;
            cursor: pointer;
            position: relative;
        }

        .radio-tile:before {
            content: "";
            position: absolute;
            display: block;
            width: 0.75rem;
            height: 0.75rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            border-radius: 50%;
            top: 0.25rem;
            left: 0.25rem;
            opacity: 0;
            transform: scale(0);
            transition: 0.25s ease;
        }

        .radio-tile:hover {
            border-color: #2260ff;
        }

        .radio-tile:hover:before {
            transform: scale(1);
            opacity: 1;
        }

        .radio-icon svg,
        .radio-icon img {
            width: 2rem;
            height: 2rem;
            fill: #494949;
        }

        .radio-label {
            color: #707070;
            transition: 0.375s ease;
            text-align: center;
            font-size: 13px;
        }

        .radio-input {
            clip: rect(0 0 0 0);
            -webkit-clip-path: inset(100%);
            clip-path: inset(100%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }

        .custom-btn {
            background-color: #F15412;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .custom-btn:hover {
            background-color: #d13e0f;
        }

        .checkbox-small {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-label {
            margin-left: 10px;
        }

        .icheck-material-teal {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icheck-material-teal img {
            width: 30px;
            height: auto;
            vertical-align: middle;
        }

        .icheck-material-teal input[type="radio"] {
            margin: 0;
        }

        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 5px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>

    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}" rel="nofollow">Trang chủ</a>
                    <span></span><a href="{{ route('shop') }}" rel="nofollow">Sản phẩm</a>
                    <span></span>Thanh toán
                </div>
            </div>
        </div>
        <section class="mt-50 mb-50">
            <div class="container">
                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <form wire:submit.prevent="placeOrder">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-25 d-flex justify-content-between align-items-center">
                                <h4>Chi Tiết Vận Chuyển</h4>
                                <button class="btn custom-btn btn-sm" wire:click.prevent="showShippingModal">Thêm Địa Chỉ Mới</button>
                            </div>

                            <!-- Modal thêm/chỉnh sửa địa chỉ -->
                            <div wire:ignore.self class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="shippingModalLabel">{{ $titleForm }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if(session('error'))
                                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center mb-4">
                                                {{ session('error') }}
                                            </div>
                                            @endif
                                            <form class="contact-form-style mt-30 mb-50">
                                                <div class="radio-inputs mb-3">
                                                    <label>
                                                        <input class="radio-input" type="radio" name="address_type" value="home" wire:model="address_type">
                                                        <span class="radio-tile">
                                                            <span class="radio-icon">
                                                                <img src="{{ asset('assets/imgs/cart/home.png') }}" alt="" style="width: 50px;">
                                                            </span>
                                                            <span class="radio-label">Nhà Riêng</span>
                                                        </span>
                                                    </label>
                                                    <label>
                                                        <input class="radio-input" type="radio" name="address_type" value="office" wire:model="address_type">
                                                        <span class="radio-tile">
                                                            <span class="radio-icon">
                                                                <img src="{{ asset('assets/imgs/cart/home.png') }}" alt="" style="width: 50px;">
                                                            </span>
                                                            <span class="radio-label">Văn Phòng</span>
                                                        </span>
                                                    </label>
                                                    <label>
                                                        <input class="radio-input" type="radio" name="address_type" value="other" wire:model="address_type">
                                                        <span class="radio-tile">
                                                            <span class="radio-icon">
                                                                <img src="{{ asset('assets/imgs/cart/home.png') }}" alt="" style="width: 50px;">
                                                            </span>
                                                            <span class="radio-label">Khác</span>
                                                        </span>
                                                    </label>
                                                </div>
                                                @error('address_type') <span class="error-message">{{ $message }}</span> @enderror

                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="input-style mb-10">
                                                            <label>Họ và Tên <span class="text-red-500">*</span></label>
                                                            <input placeholder="Nhập họ tên của bạn" type="text" class="square border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model="name">
                                                            @error('name') <span class="error-message">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div class="input-style mb-10">
                                                            <label>Số Điện Thoại <span class="text-red-500">*</span></label>
                                                            <input placeholder="Ví dụ: 07954055xxx" type="tel" class="square border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:model="phone">
                                                            @error('phone') <span class="error-message">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div class="input-style mb-10">
                                                            <label>Tỉnh/Thành Phố <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <select class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model.live="selectedProvince">
                                                                    <option value="">Chọn Tỉnh/Thành Phố</option>
                                                                    @forelse($provinces as $province)
                                                                    <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                                                    @empty
                                                                    <option value="" disabled>Không có tỉnh/thành phố</option>
                                                                    @endforelse
                                                                </select>
                                                                <span wire:loading wire:target="selectedProvince" class="loading-spinner absolute right-3 top-1/2 transform -translate-y-1/2"></span>
                                                            </div>
                                                            @error('selectedProvince') <span class="error-message">{{ $message }}</span> @enderror
                                                            @if(empty($provinces) && !$editForm)
                                                            <span class="error-message">Không thể tải danh sách tỉnh/thành. Vui lòng thử lại sau.</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="input-style mb-10">
                                                            <label>Quận/Huyện <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <select class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model.live="selectedDistrict" {{ empty($provinces) || !$selectedProvince ? 'disabled' : '' }}>
                                                                    <option value="">Chọn Quận/Huyện</option>
                                                                    @forelse($districts as $district)
                                                                    <option value="{{ $district['id'] }}">{{ $district['name'] }}</option>
                                                                    @empty
                                                                    <option value="" disabled>Không có quận/huyện</option>
                                                                    @endforelse
                                                                </select>
                                                                <span wire:loading wire:target="selectedDistrict" class="loading-spinner absolute right-3 top-1/2 transform -translate-y-1/2"></span>
                                                            </div>
                                                            @error('selectedDistrict') <span class="error-message">{{ $message }}</span> @enderror
                                                            @if($selectedProvince && empty($districts) && !$editForm)
                                                            <span class="error-message">Không thể tải danh sách quận/huyện. Vui lòng thử lại.</span>
                                                            @endif
                                                        </div>
                                                        <div class="input-style mb-10">
                                                            <label>Phường/Xã <span class="text-red-500">*</span></label>
                                                            <div class="relative">
                                                                <select class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model.live="selectedWard" {{ empty($districts) || !$selectedDistrict ? 'disabled' : '' }}>
                                                                    <option value="">Chọn Phường/Xã</option>
                                                                    @forelse($wards as $ward)
                                                                    <option value="{{ $ward['id'] }}">{{ $ward['name'] }}</option>
                                                                    @empty
                                                                    <option value="" disabled>Không có phường/xã</option>
                                                                    @endforelse
                                                                </select>
                                                                <span wire:loading wire:target="selectedWard" class="loading-spinner absolute right-3 top-1/2 transform -translate-y-1/2"></span>
                                                            </div>
                                                            @error('selectedWard') <span class="error-message">{{ $message }}</span> @enderror
                                                            @if($selectedDistrict && empty($wards) && !$editForm)
                                                            <span class="error-message">Không thể tải danh sách phường/xã. Vui lòng thử lại.</span>
                                                            @endif
                                                        </div>
                                                        <div class="input-style mb-10">
                                                            <label>Địa Chỉ Nhận Hàng <span class="text-red-500">*</span></label>
                                                            <input placeholder="Tên Đường, Tòa Nhà, Số Nhà" type="text" class="square border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model="address">
                                                            @error('address') <span class="error-message">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div class="input-style mb-10 d-flex align-items-center">
                                                            <input type="checkbox" id="status" class="square checkbox-small" wire:model="status">
                                                            <label for="status" class="checkbox-label">Đặt làm địa chỉ mặc định</label>
                                                            @error('status') <span class="error-message">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            @if($editForm)
                                            <button type="button" class="btn btn-primary" wire:click.prevent="updateShipping">Cập Nhật Địa Chỉ</button>
                                            @else
                                            <button type="button" class="btn btn-primary" wire:click.prevent="addShipping">Thêm Địa Chỉ Mới</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Danh sách địa chỉ -->
                            <div class="row">
                                <div class="col-lg-12 mb-sm-15">
                                    @forelse($shippings as $shipping)
                                    <div class="toggle_info mb-5">
                                        <div class="row d-flex align-items-center">
                                            <div class="col-6 col-md-4">
                                                <div class="radio-inputs" wire:click="selectShipping({{ $shipping->id }})">
                                                    <label>
                                                        <input class="radio-input" type="radio" name="selectedShippingId" value="{{ $shipping->id }}" wire:model="selectedShippingId">
                                                        <span class="radio-tile">
                                                            <span class="radio-icon">
                                                                <img src="{{ asset('assets/imgs/cart/home.png') }}" alt="" style="width: 50px;">
                                                            </span>
                                                            <span class="radio-label">{{ ucwords($shipping->address_type) }}</span>
                                                            @if($shipping->status)
                                                            <p style="font-size: 14px; color: rgb(12, 227, 12);">Mặc định</p>
                                                            @endif
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <p style="font-size: 14px;">Tên: {{ $shipping->name }}</p>
                                                <p style="font-size: 14px;">SĐT: {{ $shipping->phone }}</p>
                                                <p style="font-size: 14px;">Địa chỉ: {{ $shipping->province }}, {{ $shipping->district }}, {{ $shipping->ward }}</p>
                                                <p style="font-size: 14px;">Số nhà: {{ $shipping->address }}</p>
                                            </div>
                                            <div class="col-6 col-md-4 text-center">
                                                <a href="#" wire:click.prevent="showEditShipping({{ $shipping->id }})"><i class="fi-rs-pencil mr-10"></i></a>
                                                <a href="#" wire:click.prevent="deleteConfirmation({{ $shipping->id }})"><i class="fi-rs-trash mr-10"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center">
                                        Chưa có địa chỉ giao hàng. Vui lòng nhấn <strong>"Thêm Địa Chỉ Mới"</strong> để thêm địa chỉ.
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mb-10">
                                <h5 style="margin-top: 10px; font-size: 18px; margin-bottom: 10px;">Thông Tin Khác</h5>
                            </div>
                            <div class="form-group mb-30">
                                <textarea rows="5" placeholder="Ghi chú (ví dụ: giao hàng ngoài giờ hành chính)" wire:model="additional_info" class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="order_review">
                                <div class="mb-20">
                                    <h4>Đơn Đặt Hàng Của Bạn</h4>
                                </div>
                                <div class="table-responsive order_table text-center">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Kiểm Tra Lại Đơn Hàng</th>
                                                <th>Giá</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(Cart::instance('cart')->content() as $item)
                                            <tr>
                                                <td class="image product-thumbnail"><img src="{{ asset('admin/product/' . $item->model->image) }}" alt="{{ $item->model->name }}"></td>
                                                <td>
                                                    <h5><a href="{{ route('details', $item->model->slug) }}">{{ $item->model->name }}</a> <span class="product-qty"> x {{ $item->qty }}</span></h5>
                                                </td>
                                                <td>{{ number_format($item->model->sale_price, 3, ',', '.') }}đ</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3">Giỏ hàng trống</td>
                                            </tr>
                                            @endforelse
                                            <tr>
                                                <th>Tổng Giỏ Hàng</th>
                                                <td class="product-subtotal" colspan="2">{{ number_format($subtotal, 3, ',', '.') }}đ</td>
                                            </tr>
                                            @if(Session::has('coupon'))
                                            <tr>
                                                <th>Mã Giảm Giá ({{ session()->get('coupon')['coupon_code'] }})</th>
                                                <td class="product-subtotal" colspan="2">- {{ number_format($discount, 3, ',', '.') }}đ</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Phí Vận Chuyển</th>
                                                <td colspan="2">
                                                    <em>
                                                        @if(isset($shippingCost))
                                                            @if($shippingCost == 0)
                                                                Miễn phí
                                                            @else
                                                                {{ number_format($shippingCost, 3, ',', '.') }}đ
                                                            @endif
                                                        @else
                                                            Vui lòng chọn địa chỉ để tính phí
                                                        @endif
                                                    </em>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tổng Thanh Toán</th>
                                                <td colspan="2" class="product-subtotal">
                                                    <span class="font-xl text-brand fw-900">
                                                        {{ number_format($total, 3, ',', '.') }}đ
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="bt-1 border-color-1 mt-30 mb-30"></div>
                                <div class="payment_method">
                                    <div class="mb-25">
                                        <h5>Phương Thức Thanh Toán</h5>
                                    </div>
                                    <div class="payment_option">
                                        <div class="icheck-material-teal">
                                            <input type="radio" id="cod" name="paymentmode" value="COD" wire:model="paymentmode">
                                            <label for="cod"><img src="{{ asset('assets/imgs/payment/thanhtoantienmat.svg') }}" alt="Thanh Toán Tiền Mặt"> Thanh Toán Tiền Mặt Khi Nhận Hàng</label>
                                        </div>
                                        <div class="icheck-material-teal">
                                            <input type="radio" id="payos" name="paymentmode" value="PayOS" wire:model="paymentmode">
                                            <label for="payos"><img src="{{ asset('assets/imgs/payment/thanhtoanpayos.png') }}" alt="PayOS"> Thanh Toán Qua PayOS</label>
                                        </div>
                                        @error('paymentmode') <span class="error-message">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="order_button pt-25">
                                    <button type="submit" class="btn btn-fill-out btn-dark btn-block mt-30 w-100" wire:loading.attr="disabled" {{ !$selectedShippingId || !isset($shippingCost) ? 'disabled' : '' }}>
                                        <span wire:loading wire:target="placeOrder">Đang xử lý...</span>
                                        <span wire:loading.remove>XÁC NHẬN ĐẶT HÀNG</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Livewire.on('show-shipping-modal', () => {
                const modalElement = document.getElementById('shippingModal');
                if (modalElement) {
                    new bootstrap.Modal(modalElement).show();
                } else {
                    console.error('Modal element #shippingModal not found in DOM');
                }
            });

            Livewire.on('close-shipping-modal', () => {
                const modalElement = document.getElementById('shippingModal');
                if (modalElement) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        console.error('Modal instance not found for #shippingModal');
                    }

                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = 'auto';
                } else {
                    console.error('Modal element #shippingModal not found in DOM');
                }
            });
        });
    </script>
</div>