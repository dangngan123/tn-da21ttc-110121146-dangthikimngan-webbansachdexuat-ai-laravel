<div>
    <style>
        .card-header {
            padding: 10px 15px;
        }

        .shop-product-fillter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .custom-file-input {
            width: 150px;
            font-size: 12px;
        }

        .input-group-sm .custom-file,
        .input-group-sm .custom-file-input {
            height: 30px;
        }

        .input-group-append button {
            margin-left: 5px;
        }

        .custom-btn-sm {
            padding: 5px 8px;
            font-size: 12px;
        }

        .sort-by-product-area {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sidebar-widget {
            margin-bottom: 0;
        }

        .alert {
            margin-bottom: 5px;
        }

        .sort-by-cover {
            margin-right: 10px;
        }

        .checkbox-wrapper label {
            margin-right: 230px;
            font-weight: bold;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .dropdown-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }

        .dropdown-wrapper select {
            flex: 1;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 30px;
        }

        .dropdown-icon {
            position: absolute;
            right: 10px;
            pointer-events: none;
            font-size: 16px;
            color: #aaa;
        }

        .alert-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Thêm style cho modal chi tiết sản phẩm */
        .product-detail-modal .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .product-detail-modal .modal-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }

        .product-detail-modal .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .product-detail-modal .image-gallery img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .product-detail-modal .detail-label {
            font-weight: bold;
            color: #333;
        }

        .product-detail-modal .detail-value {
            color: #555;
        }
    </style>

    <div class="card">
        <!-- Modal thêm/sửa sản phẩm (giữ nguyên) -->
        <div wire:ignore.self class="modal fade" id="productModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{$titleForm}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click.prevent="resetForm"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <form class="contact-form-style mt-30 mb-50" action="#" method="post">
                                    @error('address_type') <span class="error text-danger">{{ $message }}</span> @enderror
                                    <div class="col-lg-6">
                                        <div class="input-style mb-10">
                                            <label>Tên sản phẩm</label>
                                            <input name="order-id" placeholder="Nhập tên sản phẩm..." type="text" class="square" wire:model="name" wire:keyup="generateSlug">
                                            @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="input-style mb-10 hidden">
                                            <label>Slug</label>
                                            <input name="order-id" placeholder="Nhập slug..." type="text" class="square" wire:model="slug">
                                            @error('slug') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        @if ($showModal)
                                        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                                            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                                                <h2 class="text-xl font-bold mb-4">Nhập chi tiết mô tả sản phẩm</h2>
                                                <textarea
                                                    wire:model="productDetails"
                                                    class="w-full p-2 border rounded"
                                                    rows="5"
                                                    placeholder="Ví dụ: Sách Búp Sen Xanh là tiểu sử Hồ Chí Minh, dành cho trẻ em..."></textarea>
                                                @error('productDetails') <span class="text-red-500 text-sm">{{ $errors->first('productDetails') }}</span> @enderror
                                                <div class="mt-4 flex justify-end gap-2">
                                                    <button
                                                        wire:click.prevent="generateDescription"
                                                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                                                        Tạo mô tả
                                                    </button>
                                                    <button
                                                        wire:click.prevent="closeModal"
                                                        class="px-4 py-2 rounded hover:bg-red-600"
                                                        style="background-color: #ef4444; color: white;">
                                                        Hủy
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if (session()->has('message'))
                                        <div id="successMessage" class="alert alert-success">
                                            {{ session('message') }}
                                        </div>
                                        <script>
                                            setTimeout(function() {
                                                var message = document.getElementById('successMessage');
                                                if (message) {
                                                    message.style.display = 'none';
                                                }
                                            }, 5000);
                                        </script>
                                        @endif

                                        @if (session()->has('error'))
                                        <div id="errorMessage" class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                        <script>
                                            setTimeout(function() {
                                                var message = document.getElementById('errorMessage');
                                                if (message) {
                                                    message.style.display = 'none';
                                                }
                                            }, 5000);
                                        </script>
                                        @endif

                                        <div class="input-style mb-10">
                                            <div class="flex items-center justify-between mb-1">
                                                <label>Mô tả ngắn</label>
                                                <span wire:click="openModal" class="cursor-pointer">
                                                    <i class="fa-solid fa-wand-sparkles text-red-500"></i>
                                                </span>
                                            </div>
                                            <textarea placeholder="Nhập mô tả ngắn..."
                                                class="square overflow-y-auto resize-none w-full p-2"
                                                wire:model="short_description"></textarea>
                                            @error('short_description') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <div class="flex items-center justify-between mb-1">
                                                <label>Mô tả dài</label>
                                                <span wire:click="openModal" class="cursor-pointer">
                                                    <i class="fa-solid fa-wand-sparkles text-red-500"></i>
                                                </span>
                                            </div>
                                            <textarea placeholder="Nhập mô tả dài..."
                                                class="square overflow-y-auto resize-none w-full p-2"
                                                wire:model="long_description"></textarea>
                                            @error('long_description') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <div class="flex items-center justify-between mb-1">
                                                <label>Nhà xuất bản</label>
                                                <span wire:click="openModal" class="cursor-pointer">
                                                    <i class="fa-solid fa-wand-sparkles text-red-500"></i>
                                                </span>
                                            </div>
                                            <input name="order-id" placeholder="Nhập Nhà xuất bản..." type="text"
                                                class="square" wire:model="publisher">
                                            @error('publisher') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <div class="flex items-center justify-between mb-1">
                                                <label>Tác giả</label>
                                                <span wire:click="openModal" class="cursor-pointer">
                                                    <i class="fa-solid fa-wand-sparkles text-red-500"></i>
                                                </span>
                                            </div>
                                            <input name="order-id" placeholder="Nhập Tác giả..." type="text"
                                                class="square" wire:model="author">
                                            @error('author') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Độ tuổi</label>
                                            <input name="order-id" placeholder="Độ tuổi..." type="text" class="square" wire:model="age">
                                            @error('age') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="reguler_price">Giá gốc <span class="text-danger">*</span></label>
                                            <input type="number" wire:model="reguler_price" class="form-control" step="0.01" required>
                                            @error('reguler_price') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="discount_type">Loại giảm giá</label>
                                            <select wire:model="discount_type" class="form-control" id="discount_type">
                                                <option value="">Không giảm giá</option>
                                                <option value="fixed">Giá cố định</option>
                                                <option value="percentage">Phần trăm</option>
                                            </select>
                                            @error('discount_type') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="discount_value">Giá trị giảm</label>
                                            <input type="number" wire:model="discount_value" class="form-control" step="0.01" placeholder="Nhập số tiền hoặc phần trăm">
                                            @error('discount_value') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="sale_price">Giá sau giảm (tự động tính toán)</label>
                                            <input type="number" wire:model="sale_price" class="form-control" step="0.01" readonly>
                                            @error('sale_price') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Số lượng</label>
                                            <input name="order-id" placeholder="Số lượng..." type="text" class="square" wire:model="quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            @error('quantity') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10" style="border: 1px solid #ccc;border-radius: 2px;">
                                            <label>Sản phẩm Hot</label>
                                            <input type="checkbox" id="is_hot" wire:model="is_hot" wire:change="updateIsHot($event.target.checked)" style="height: 20px; width: 20px; margin-left:10px ;">
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Ảnh chính</label>
                                            <input name="order-id" placeholder="Tên Đường, Tòa Nhà, Số Nhà" type="file" class="square" wire:model="image">
                                            @if ($image)
                                            <div class="image-preview">
                                                <p style="font-size: 10px">Xem ảnh trước khi tải lên:</p>
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="width: 50px">
                                                    <button type="button" wire:click="removeImage('main')" class="btn-close position-absolute top-0 end-0 bg-danger rounded-circle" style="padding: 4px"></button>
                                                </div>
                                            </div>
                                            @elseif($new_image)
                                            <img src="{{ asset('admin/product/'.$new_image) }}" class="img-thumbnail" style="width: 50px">
                                            @endif
                                            @error('image') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Ảnh phụ</label>
                                            <input name="order-id" placeholder="Ảnh..." type="file" class="square" wire:model="images">
                                            @if ($images)
                                            <div class="image-preview">
                                                <p style="font-size: 10px">Xem ảnh trước khi tải lên:</p>
                                                @foreach ($images as $index => $image)
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="width: 50px">
                                                    <button type="button" wire:click="removeImage('additional', {{ $index }})" class="btn-close position-absolute top-0 end-0 bg-danger rounded-circle" style="padding: 4px"></button>
                                                </div>
                                                @endforeach
                                            </div>
                                            @elseif($new_images)
                                            @foreach ($new_images as $image)
                                            <img src="{{ asset('admin/product/'.$image) }}" class="img-thumbnail" style="width: 50px">
                                            @endforeach
                                            @endif
                                            @error('images') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Danh mục</label>
                                            <div class="col-md-12 position-relative">
                                                <div class="dropdown-wrapper">
                                                    <select class="form-control" wire:model="category_id">
                                                        <option value="">Chọn danh mục</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="fi-rs-angle-small-down dropdown-icon"></span>
                                                </div>
                                                @error('category_id') <p class="text-danger">{{$message}}</p> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        @if($editForm)
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="resetForm">Close</button>
                        <button type="button" class="btn btn-primary" wire:click.prevent="updateProduct()">Cập nhật phẩm mới</button>
                        @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="resetForm">Close</button>
                        <button type="button" class="btn btn-primary" wire:click.prevent="addProduct()">Thêm sản phẩm</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal chi tiết sản phẩm -->
        <div wire:ignore.self class="modal fade product-detail-modal" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productDetailModalLabel">Chi tiết sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($selectedProduct)
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="detail-label">Tên sản phẩm:</span>
                                    <span class="detail-value">{{ $selectedProduct->name }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Slug:</span>
                                    <span class="detail-value">{{ $selectedProduct->slug }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Mô tả ngắn:</span>
                                    <p class="detail-value">{{ $selectedProduct->short_description }}</p>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Mô tả dài:</span>
                                    <p class="detail-value">{{ $selectedProduct->long_description }}</p>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Nhà xuất bản:</span>
                                    <span class="detail-value">{{ $selectedProduct->publisher ?? 'Chưa có' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Tác giả:</span>
                                    <span class="detail-value">{{ $selectedProduct->author ?? 'Chưa có' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Độ tuổi:</span>
                                    <span class="detail-value">{{ $selectedProduct->age ?? 'Chưa có' }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <span class="detail-label">Giá gốc:</span>
                                    <span class="detail-value">{{ number_format($selectedProduct->reguler_price, 3, ',', '.') }}đ</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Giá sau giảm:</span>
                                    <span class="detail-value">{{ $selectedProduct->sale_price ? number_format($selectedProduct->sale_price, 3, ',', '.') . 'đ' : 'Chưa có' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Loại giảm giá:</span>
                                    <span class="detail-value">{{ $selectedProduct->discount_type ? ($selectedProduct->discount_type == 'fixed' ? 'Giá cố định' : 'Phần trăm') : 'Không giảm giá' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Giá trị giảm:</span>
                                    <span class="detail-value">{{ $selectedProduct->discount_value ? number_format($selectedProduct->discount_value, 2) . ($selectedProduct->discount_type == 'percentage' ? '%' : 'đ') : 'Chưa có' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Số lượng:</span>
                                    <span class="detail-value">{{ $selectedProduct->quantity }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Số lượng đã bán:</span>
                                    <span class="detail-value">{{ $selectedProduct->sold_count }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Danh mục:</span>
                                    <span class="detail-value">{{ $selectedProduct->category->name ?? 'Chưa có' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Sản phẩm nổi bật:</span>
                                    <span class="detail-value">{{ $selectedProduct->is_hot ? 'Có' : 'Không' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="detail-label">Ảnh sản phẩm:</span>
                                    <div class="image-gallery">
                                        <img src="{{ asset('admin/product/' . $selectedProduct->image) }}" alt="Main Image">
                                        @if($selectedProduct->images)
                                            @foreach(explode(',', $selectedProduct->images) as $image)
                                                @if($image)
                                                    <img src="{{ asset('admin/product/' . $image) }}" alt="Additional Image">
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <p class="text-center text-danger">Không có thông tin sản phẩm.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-header">
            <div class="shop-product-fillter mb-0">
                <div>
                    <a href="#" class="btn-sm btn-primary ml-3" wire:click.prevent="showProductModal">Thêm sản phẩm</a>
                </div>

                <div class="totall-product">
                    <div class="sidebar-widget widget_search bg-2">
                        <div class="search-form">
                            <form action="#">
                                <input type="text" placeholder="Tìm kiếm…" wire:model.live="search" style="width: 440px;">
                            </form>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center">
                    <div class="input-group input-group-sm mb-1">
                        <div class="custom-file">
                            <input type="file" wire:model="file" class="custom-file-input">
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-primary custom-btn-sm" type="button" wire:click.prevent="import">
                                <i class="fi-rs-upload mr-2"></i> Import
                            </button>
                        </div>
                    </div>
                </div>

                @if (session()->has('message'))
                <div class="alert alert-success"
                    x-data="{show: true}"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 5000)"
                    role="alert">
                    Nhập dữ liệu thành công!
                </div>
                @endif

                @if (session()->has('error'))
                <div class="alert alert-danger"
                    x-data="{show: true}"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 5000)"
                    role="alert">
                    Có lỗi xảy ra: {{ session('error') }}
                </div>
                @endif

                <div class="sort-by-product-area align-items-center">
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
                                <li><a href="#" wire:click.prevent="selecteDelete"><i class="fi-rs-trash"></i> Xóa</a></li>
                                <li><a href="#" wire:click.prevent="export"><i class="fi-rs-download mr-5"></i>Export</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="me-3" style="font-size: 14px; line-height: 1.5;">
                            <div style="position: relative;">
                                <select class="form-control" wire:model.live="statusFilter" style="border-radius: 5px; background-color: #f1f1f1; color: #333; font-size: 14px; padding-left: 30px;">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="in_stock">Còn hàng</option>
                                    <option value="low_stock">Sắp hết hàng</option>
                                    <option value="out_of_stock">Hết hàng</option>
                                </select>
                                <i class="fa-solid fa-filter" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #007bff;"></i>
                            </div>
                        </label>

                        <label class="me-3" style="font-size: 14px; line-height: 1.5;">
                            <div style="position: relative;">
                                <select class="form-control" wire:model.live="categoryFilter" style="border-radius: 5px; background-color: #f1f1f1; color: #333; font-size: 14px; padding-left: 30px;">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-list" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #007bff;"></i>
                            </div>
                        </label>
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
                                <li><a class="{{ $pagesize == 48 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(48)">50</a></li>
                                <li><a class="{{ $pagesize == 50 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(50)">100</a></li>
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
                        <tr>
                            <th><input type="checkbox" wire:model.live="selectAll" class="small-checkbox"></th>
                            <th class="text-center">STT</th>
                            <th class="text-center">Tên sản phẩm</th>
                            <th class="text-center" style="color: #F15412;">Giá bán</th>
                            <th class="text-center">Danh mục</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-center">Ảnh</th>
                            <th class="text-center">Trạng thái</th>
                            <th colspan="3" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                        <tr class="{{$this->isColor($product->id)}}">
                            <td class="small-checkbox">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $product->id }}">
                            </td>
                            <td class="text-center">{{$index + $products->firstItem()}}</td>
                            <td>{{ $product->name }}</td>
                            <td style="color: #F15412;">{{ number_format($product->reguler_price, 3, ',', '.') }}đ</td>
                            <td>{{ $product->category->name ?? '' }}</td>
                            <td class="text-center">{{ $product->quantity }}</td>
                            <td><a href="{{ $product->getImage()}}" data-lightbox="example-1"><img src="{{ $product->getImage()}}" alt="Slider Image" style="width:80px"></a></td>
                            <td class="text-center">
                                @if($product->quantity == 0)
                                <span class="badge bg-danger">Hết hàng</span>
                                @elseif($product->quantity <= 10)
                                    <span class="badge bg-warning">Sắp hết hàng</span>
                                    @else
                                    <span class="badge bg-success">Còn hàng</span>
                                    @endif
                            </td>
                           
                            <td><a href="#" class="btn-small d-block btn-sm btn-success" wire:click.prevent="showEditProduct({{$product->id}})">Sửa</a></td>
                            <td><a href="#" class="btn-small d-block btn-sm btn-danger" wire:click.prevent="deleteConfirmation({{$product->id}})">Xóa</a></td>
                             <td><a href="#" class="btn-small d-block btn-sm btn-info" wire:click.prevent="showProductDetail({{$product->id}})">   <i class="bi bi-eye-fill me-1"></i></a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-danger">Không có sản phẩm nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{$products->links()}}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('product-modal', () => {
                const regulerPriceInput = document.querySelector('[wire\\:model="reguler_price"]');
                const discountTypeSelect = document.querySelector('[wire\\:model="discount_type"]');
                const discountValueInput = document.querySelector('[wire\\:model="discount_value"]');
                const salePriceInput = document.querySelector('[wire\\:model="sale_price"]');

                if (!regulerPriceInput || !discountTypeSelect || !discountValueInput || !salePriceInput) {
                    console.error('Một hoặc nhiều phần tử không tồn tại trong DOM');
                    return;
                }

                function calculateSalePrice() {
                    const regulerPrice = parseFloat(regulerPriceInput.value) || 0;
                    const discountType = discountTypeSelect.value;
                    const discountValue = parseFloat(discountValueInput.value) || 0;
                    let salePrice = regulerPrice;

                    if (discountType === 'fixed') {
                        salePrice = regulerPrice - discountValue;
                    } else if (discountType === 'percentage') {
                        salePrice = regulerPrice * (1 - discountValue / 100);
                    }

                    salePriceInput.value = salePrice > 0 ? salePrice.toFixed(2) : 0;
                    Livewire.emit('setSalePrice', salePrice > 0 ? salePrice : 0);
                }

                regulerPriceInput.addEventListener('input', calculateSalePrice);
                discountTypeSelect.addEventListener('change', calculateSalePrice);
                discountValueInput.addEventListener('input', calculateSalePrice);
                calculateSalePrice();
            });

            // Mở modal chi tiết sản phẩm
            Livewire.on('show-product-detail-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
                modal.show();
            });
        });
    </script>
</div>