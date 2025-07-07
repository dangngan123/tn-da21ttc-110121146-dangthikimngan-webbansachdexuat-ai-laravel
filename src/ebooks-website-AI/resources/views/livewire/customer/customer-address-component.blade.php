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

        .radio-icon img {
            width: 2rem;
            height: 2rem;
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

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Modal thêm/chỉnh sửa địa chỉ -->
    <div wire:ignore.self class="modal fade" id="ShippingModal" tabindex="-1" aria-labelledby="ShippingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ShippingModalLabel">{{ $titleForm }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($errorMessage)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ $errorMessage }}
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
                                        <select class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model.live="province">
                                            <option value="">Chọn Tỉnh/Thành Phố</option>
                                            @forelse($provinces as $province)
                                                <option value="{{ $province['name'] }}">{{ $province['name'] }}</option>
                                            @empty
                                                <option value="" disabled>Không có tỉnh/thành phố</option>
                                            @endforelse
                                        </select>
                                        <span wire:loading wire:target="province" class="loading-spinner absolute right-3 top-1/2 transform -translate-y-1/2"></span>
                                    </div>
                                    @error('province') <span class="error-message">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-style mb-10">
                                    <label>Quận/Huyện <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model.live="district" {{ !$province ? 'disabled' : '' }}>
                                            <option value="">Chọn Quận/Huyện</option>
                                            @forelse($districts as $district)
                                                <option value="{{ $district['name'] }}">{{ $district['name'] }}</option>
                                            @empty
                                                <option value="" disabled>Không có quận/huyện</option>
                                            @endforelse
                                        </select>
                                        <span wire:loading wire:target="district" class="loading-spinner absolute right-3 top-1/2 transform -translate-y-1/2"></span>
                                    </div>
                                    @error('district') <span class="error-message">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-style mb-10">
                                    <label>Phường/Xã <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select class="form-control border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model.live="ward" {{ !$district ? 'disabled' : '' }}>
                                            <option value="">Chọn Phường/Xã</option>
                                            @forelse($wards as $ward)
                                                <option value="{{ $ward['name'] }}">{{ $ward['name'] }}</option>
                                            @empty
                                                <option value="" disabled>Không có phường/xã</option>
                                            @endforelse
                                        </select>
                                        <span wire:loading wire:target="ward" class="loading-spinner absolute right-3 top-1/2 transform -translate-y-1/2"></span>
                                    </div>
                                    @error('ward') <span class="error-message">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-style mb-10">
                                    <label>Địa Chỉ Nhận Hàng <span class="text-red-500">*</span></label>
                                    <input placeholder="Tên Đường, Tòa Nhà, Số Nhà" type="text" class="square border border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" wire:model="address">
                                    @error('address') <span class="error-message">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-style mb-10 d-flex align-items-center">
                                    <input type="checkbox" id="status" class="square checkbox-small" wire:model="status" wire:change="updateStatus($event.target.checked)">
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
    <div class="container">
        @if(session('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-center mb-4">
                {{ session('message') }}
            </div>
        @endif
        @if($errorMessage)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center mb-4">
                {{ $errorMessage }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="mb-25 d-flex justify-content-between align-items-center">
                    <h4>Danh Sách Địa Chỉ</h4>
                    <button class="btn custom-btn btn-sm" wire:click.prevent="showShippingModal">Thêm Địa Chỉ Mới</button>
                </div>
                <div class="row">
                    <div class="col-lg-12 mb-sm-20">
                        @forelse($shippings as $shipping)
                            <div class="toggle_info mb-5 p-3" style="border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); min-height: 150px; width: 100%; max-width: 900px; margin: 0 auto;">
                                <div class="row d-flex align-items-center">
                                    <div class="col-6 col-md-3">
                                        <div class="radio-inputs" style="cursor: pointer;">
                                            <label>
                                                <input class="radio-input" type="radio" name="address_type" value="{{ $shipping->address_type }}" wire:model="address_type">
                                                <span class="radio-tile d-flex align-items-center" style="padding: 10px; border: 1px solid #ccc; border-radius: 8px; background: #fff; transition: all 0.3s; width: 100%;">
                                                    <span class="radio-icon">
                                                        <img src="{{ asset('assets/imgs/cart/home.png') }}" alt="" style="width: 50px;">
                                                    </span>
                                                    <span class="radio-label" style="margin: 10px; font-size: 16px; font-weight: bold;">
                                                        {{ ucwords($shipping->address_type) }}
                                                    </span>
                                                    @if($shipping->status)
                                                        <p style="font-size: 14px; color: rgb(12, 227, 12);">Mặc định</p>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <p style="font-size: 14px; margin: 5px 0;">Tên: <strong>{{ $shipping->name }}</strong></p>
                                        <p style="font-size: 14px; margin: 5px 0;">SĐT: <strong>{{ $shipping->phone }}</strong></p>
                                        <p style="font-size: 14px; margin: 5px 0;">Địa chỉ: <strong>{{ $shipping->province }}, {{ $shipping->district }}, {{ $shipping->ward }}</strong></p>
                                        <p style="font-size: 14px; margin: 5px 0;">Số nhà: <strong>{{ $shipping->address }}</strong></p>
                                    </div>
                                    <div class="col-6 col-md-3 text-center">
                                        <a href="#" wire:click.prevent="showEditShipping({{ $shipping->id }})" style="color: #5bc0de; font-size: 18px; margin-right: 10px;">
                                            <i class="fi-rs-pencil"></i>
                                        </a>
                                        <a href="#" wire:click.prevent="deleteConfirmation({{ $shipping->id }})" style="color: #d9534f; font-size: 18px;">
                                            <i class="fi-rs-trash"></i>
                                        </a>
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
            </div>
        </div>
    </div>

    <!-- JavaScript cho modal và xác nhận xóa -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Livewire.on('show-shipping-modal', () => {
                console.log('show-shipping-modal event received');
                const modalElement = document.getElementById('ShippingModal');
                if (modalElement) {
                    new bootstrap.Modal(modalElement).show();
                } else {
                    console.error('Modal element #ShippingModal not found in DOM');
                }
            });

            // Livewire.on('show-delete-confirmation', () => {
            //     if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
            //         Livewire.call('deleteShipping');
            //     }
            // });

            // Livewire.on('ShippingDeleted', () => {
            //     alert('Địa chỉ đã được xóa!');
            // });

            // Livewire.on('DeleteFailed', () => {
            //     alert('Xóa địa chỉ thất bại!');
            // });
        });
    </script>
</div>