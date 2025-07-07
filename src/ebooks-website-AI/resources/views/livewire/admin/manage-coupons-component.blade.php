<div>
    <style>
        .styled-select {
            padding: 10px;
            position: relative;
        }

        .styled-select label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .styled-select select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
            color: #333;
            appearance: none;
        }

        .styled-select::after {
            content: "▼";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            pointer-events: none;
        }

        .styled-select .error {
            font-size: 12px;
            color: #e74c3c;
            margin-top: 5px;
        }

        .input-style input,
        .input-style textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .input-style label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .input-style .error {
            font-size: 12px;
            color: #e74c3c;
            margin-top: 5px;
        }

        .btn-xs {
            padding: 2px 5px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .filter-bar {
            margin-bottom: 20px;
        }

        .coupon-detail-modal .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .coupon-detail-modal .modal-header {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            border-bottom: 1px solid #ccc;
        }

        .coupon-detail-modal .detail-label {
            font-weight: bold;
            color: #333;
        }

        .coupon-detail-modal .detail-value {
            color: #555;
        }

        .pagination {
            margin-top: 20px;
            justify-content: center;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="btn-sm btn-primary ml-3" wire:click.prevent="showCouponModal">Thêm mã giảm giá</a>
                    <div class="sidebar-widget widget_search" style="background-color:rgb(255, 255, 255);">
                        <input type="text" placeholder="Tìm mã giảm giá..." wire:model.live="search">
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="me-3" style="font-size: 14px; line-height: 1.5;">
                            <div style="position: relative;">
                                <select class="form-control" wire:model.live="filter_type" style="border-radius: 5px; background-color: #f1f1f1; color: #333; font-size: 14px; padding-left: 30px;">
                                    <option value="">Loại giảm giá</option>
                                    <option value="fixed">Cố định</option>
                                    <option value="percent">Phần trăm</option>
                                </select>
                                <i class="fa-solid fa-filter" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #007bff;"></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="couponModalLabel">{{ $titleForm }}</h5>
                        <button type="button" class="btn-close" wire:click.prevent="resetForm" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-style mb-10">
                                        <label>Mã giảm giá <span class="text-danger">*</span></label>
                                        <input type="text" placeholder="VD: SUMMER25" wire:model="coupon_code">
                                        @error('coupon_code') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10 styled-select">
                                        <label>Loại giảm giá <span class="text-danger">*</span></label>
                                        <select wire:model="coupon_type">
                                            <option value="">Chọn loại giảm...</option>
                                            <option value="fixed">Cố định (VNĐ)</option>
                                            <option value="percent">Phần trăm (%)</option>
                                        </select>
                                        @error('coupon_type') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10">
                                        <label>Giá trị giảm <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" placeholder="VD: 100000 hoặc 10" wire:model="coupon_value">
                                        @error('coupon_value') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10">
                                        <label>Giá trị đơn tối thiểu <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" placeholder="VD: 500000" wire:model="cart_value">
                                        @error('cart_value') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10">
                                        <label>Ngày bắt đầu</label>
                                        <input type="date" placeholder="Chọn ngày bắt đầu..." wire:model="start_date">
                                        @error('start_date') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10">
                                        <label>ID người dùng</label>
                                        <input type="text" placeholder="Nhập ID người dùng (để trống cho tất cả)" wire:model="user_id">
                                        @error('user_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-style mb-10">
                                        <label>Ngày kết thúc <span class="text-danger">*</span></label>
                                        <input type="date" placeholder="Chọn ngày kết thúc..." wire:model="end_date">
                                        @error('end_date') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10">
                                        <label>Số lần sử dụng tối đa</label>
                                        <input type="number" placeholder="VD: 100" wire:model="max_uses">
                                        @error('max_uses') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="input-style mb-10 styled-select">
                                        <label>Trạng thái</label>
                                        <select wire:model="is_active">
                                            <option value="1">Kích hoạt</option>
                                            <option value="0">Tắt</option>
                                        </select>
                                        @error('is_active') <span class="error text-danger">{{ $message }}</span> @endif
                                    </div>
                                    <div class="input-style mb-10">
                                        <label>Mô tả</label>
                                        <textarea placeholder="Mô tả khuyến mãi..." wire:model="description" rows="3"></textarea>
                                        @error('description') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <label class="input-style mb-10 styled-select">
                                        <label>Chọn danh mục</label>
                                        <select wire:model="category_ids" multiple>
                                            @foreach($categories as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_ids') <span class="error text-danger">{{ $message }}</span> @endif
                                    </label>
                                    <label class="input-style mb-10 styled-select">
                                        <label>Chọn sản phẩm</label>
                                        <select wire:model="product_ids" multiple>
                                            @foreach($products as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('product_ids') <span class="error text-danger">{{ $message }}</span> @endif
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click.prevent="resetForm">Đóng</button>
                        @if($editForm)
                        <button type="button" class="btn btn-primary" wire:click.prevent="updateCoupon">Cập nhật</button>
                        @else
                        <button type="button" class="btn btn-primary" wire:click.prevent="addCoupon">Thêm</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade coupon-detail-modal" id="couponDetailModal" tabindex="-1" aria-labelledby="couponDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="couponDetailModalLabel">Chi tiết mã giảm giá: {{ $selectedCoupon ? $selectedCoupon->coupon_code : '' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($selectedCoupon)
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="detail-label">Mã giảm giá:</span>
                                    <span class="detail-value">{{ $selectedCoupon->coupon_code }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Loại giảm giá:</span>
                                    <span class="detail-value">{{ $selectedCoupon->coupon_type == 'fixed' ? 'Cố định (VNĐ)' : 'Phần trăm (%)' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Giá trị giảm:</span>
                                    <span class="detail-value">{{ $selectedCoupon->coupon_type == 'percent' ? $selectedCoupon->coupon_value . '%' : number_format($selectedCoupon->coupon_value, 2) }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Giá trị đơn tối thiểu:</span>
                                    <span class="detail-value">{{ number_format($selectedCoupon->cart_value, 2) }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Danh mục áp dụng:</span>
                                    <span class="detail-value">{{ $selectedCoupon->categories }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Sản phẩm áp dụng:</span>
                                    <span class="detail-value">{{ $selectedCoupon->products }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Áp dụng cho người dùng:</span>
                                    <span class="detail-value">{{ $selectedCoupon->user_id ? \App\Models\User::find($selectedCoupon->user_id)->name ?? 'N/A' : 'Tất cả' }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="detail-label">Ngày bắt đầu:</span>
                                    <span class="detail-value">{{ $selectedCoupon->start_date ? \Carbon\Carbon::parse($selectedCoupon->start_date)->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Ngày kết thúc:</span>
                                    <span class="detail-value">{{ $selectedCoupon->end_date ? \Carbon\Carbon::parse($selectedCoupon->end_date)->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Số lần sử dụng tối đa:</span>
                                    <span class="detail-value">{{ $selectedCoupon->max_uses ?? 'Không giới hạn' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Số lần đã sử dụng:</span>
                                    <span class="detail-value">{{ $selectedCoupon->used ?? 0 }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Số lần sử dụng còn lại (mỗi người):</span>
                                    <span class="detail-value">{{ $selectedCoupon->max_uses_per_user ? ($selectedCoupon->max_uses_per_user - ($selectedCoupon->user_usage_count ?? 0)) : 'N/A' }} (Giới hạn: {{ $selectedCoupon->max_uses_per_user ?? 'N/A' }} lần/người)</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Trạng thái:</span>
                                    <span class="detail-value">{{ $selectedCoupon->is_active ? 'Kích hoạt' : 'Tắt' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Mô tả:</span>
                                    <span class="detail-value">{{ $selectedCoupon->description ?? 'N/A' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Ngày tạo:</span>
                                    <span class="detail-value">{{ $selectedCoupon->created_at ? $selectedCoupon->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Ngày cập nhật:</span>
                                    <span class="detail-value">{{ $selectedCoupon->updated_at ? $selectedCoupon->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        @else
                        <p class="text-center text-danger">Không có thông tin mã giảm giá.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 justify-content-center" role="list">
                @forelse($coupons as $index => $coupon)
                <div class="card p-3" style="width: 300px; border-radius: 10px; border: 1px solid #ddd; position: relative;" role="listitem">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">{{ $coupon->coupon_code }}</h5>
                        @if($coupon->end_date && \Carbon\Carbon::now()->gt($coupon->end_date))
                        <span class="badge bg-secondary">Hết hạn</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>Giảm giá:</strong>
                        <span class="text-success">
                            {{ $coupon->coupon_type == 'percent' ? $coupon->coupon_value . '%' : number_format($coupon->coupon_value, 0) . 'đ' }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Đã sử dụng:</strong>
                        <span>{{ $coupon->used ?? 0 }}/{{ $coupon->max_uses ?? 'Không giới hạn' }}</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: {{ $coupon->max_uses ? min(100, (($coupon->used ?? 0) / $coupon->max_uses) * 100) : 0 }}%;" aria-valuenow="{{ $coupon->used ?? 0 }}" aria-valuemin="0" aria-valuemax="{{ $coupon->max_uses ?? 100 }}"></div>
                    </div>
                    <div class="mb-2">
                        <strong>Ngày hết hạn:</strong>
                        <span>{{ $coupon->end_date ? \Carbon\Carbon::parse($coupon->end_date)->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if(!$coupon->is_used)
                            <button class="btn btn-sm btn-success btn-xs me-1" wire:click="showEditCoupon({{ $coupon->id }})">Sửa</button>
                            <button class="btn btn-sm btn-danger btn-xs me-1" wire:click="deleteConfirmation({{ $coupon->id }})">Xóa</button>
                            @else
                            <span class="badge bg-danger text-white">Đã sử dụng</span>
                            @endif
                        </div>
                        <a href="#" class="btn btn-sm btn-info" wire:click="showCouponDetail({{ $coupon->id }})">
                            <i class="bi bi-eye-fill me-1"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-danger w-100">Không có mã giảm giá nào</div>
                @endforelse
            </div>
            <div class="pagination">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('coupon-modal', () => {
                const modalElement = document.getElementById('couponModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement, { backdrop: true });
                    modal.show();
                }
            });

            Livewire.on('close-coupon-modal', () => {
                const modalElement = document.getElementById('couponModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                        modalElement.addEventListener('hidden.bs.modal', () => {
                            const backdrops = document.querySelectorAll('.modal-backdrop');
                            backdrops.forEach(backdrop => backdrop.remove());
                            document.body.classList.remove('modal-open');
                            document.body.style = '';
                        }, { once: true });
                    } else {
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style = '';
                    }
                }
            });

            Livewire.on('show-coupon-detail-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('couponDetailModal'));
                modal.show();
            });
        });
    </script>
</div>