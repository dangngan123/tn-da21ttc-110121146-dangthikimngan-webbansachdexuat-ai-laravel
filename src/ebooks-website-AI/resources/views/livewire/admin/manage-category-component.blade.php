<div>
    <div class="card">
        <!-- Form thêm/chỉnh sửa danh mục -->
        <div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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

                                    <div class="col-lg-6">
                                        <div class="input-style mb-10">
                                            <label>Tên danh mục</label>
                                            <input name="order-id" placeholder="Nhập tên danh mục..." type="text" class="square" wire:model="name" wire:keyup="generateSlug">
                                            @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="input-style mb-10 hidden">
                                            <label>Slug</label>
                                            <input name="order-id" placeholder="Nhập slug..." type="text" class="square" wire:model="slug">
                                            @error('slug') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="status_option">
                                            <label>Trạng thái</label> <br>
                                            <div class="icheck-material-teal incheck-inline" style="display: inline-block; margin-right: 15px;">
                                                <input type="radio" id="categoryStatusActive" name="categoryStatus" value="1" wire:model="status" />
                                                <label for="categoryStatusActive">Bật</label>
                                            </div>
                                            <div class="icheck-material-teal incheck-inline" style="display: inline-block;">
                                                <input type="radio" id="categoryStatusInactive" name="categoryStatus" value="0" wire:model="status" />
                                                <label for="categoryStatusInactive">Tắt</label>
                                            </div>
                                        </div>
                                        @error('status') <span class="error text-danger">{{ $message }}</span> @enderror


                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-style mb-10">
                                            <label>Ảnh</label>
                                            <input name="order-id" placeholder="Nhập ảnh" type="file" class="square" wire:model="image" id="{{$rand}}">
                                            @if ($image)
                                            Xem ảnh trước khi tải lên:
                                            <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="width: 100px">
                                            @elseif($new_image)
                                            <img src="{{asset('admin/category/'.$new_image)}}" class="img-thumbnail" style="width: 100px">
                                            @endif
                                            @error('image') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        @if($editForm)
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="resetForm">Close</button>
                        <button type="button" class="btn btn-primary" wire:click.prevent="updateCategory()">Cập nhật danh sách</button>
                        @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="resetForm">Close</button>
                        <button type="button" class="btn btn-primary" wire:click.prevent="addCategory()">Thêm danh sách</button>
                        @endif
                    </div>


                </div>
            </div>
        </div>
        <!-- kết thúc thêm/chỉnh sửa danh mục -->






























        <div class="card-header">
            <!-- Lọc slider -->
            <div class="shop-product-fillter mb-0">
                <!-- nút thêm danh mục -->
                <div style="display: flex; align-items: center; gap: 20px;">
                    <!-- Nút thêm danh mục -->
                    <a href="#" class="btn-sm btn-primary ml-3" wire:click.prevent="showCategoryModal">Thêm danh mục</a>

                    <!-- Khối tìm kiếm -->
                    <div class="totall-product">
                        <div class="sidebar-widget widget_search" style="background-color: #fff;">
                            <div class="search-form">
                                <form action="#">
                                    <input type="text" placeholder="Tìm kiếm…" wire:model.live="search" style="width: 555px;">
                                    <!-- <button type="submit"> <i class="fi-rs-search"></i> </button> -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Lọc sản phẩm -->
                <div class="sort-by-product-area">
                    <div class="sort-by-cover mr-10">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps"></i>Đã chọn:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> {{count($selectedItems)}} <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul class="menu">
                                <li><a href="#" wire:click.prevent="selecteDelete"><i class="fi-rs-trash"></i> Xóa</a></li>
                                <li><a href="#" wire:click.prevent="selecteActive(1)"><i class="fi-rs-thumbs-up mr-5"></i>Bật</a></li>
                                <li><a href="#" wire:click.prevent="selecteInactive(0)"><i class="fi-rs-thumbs-down mr-5"></i>Tắt</a></li>
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




























            <style>
                .small-checkbox {
                    width: 16px;
                    /* Đặt chiều rộng nhỏ */
                    height: 16px;
                    /* Đặt chiều cao nhỏ */
                    margin: 0;
                    /* Loại bỏ khoảng cách thừa */
                }
            </style>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" wire:model.live="selectAll" class="small-checkbox"></th>
                            <th class="text-center">Thứ tự</th>
                            <th class="text-center">Tên danh mục</th>
                            <!-- <th>Slug</th> -->
                            <th class="text-center">Ảnh</th>
                            <th class="text-center">Trạng thái</th>
                            <th colspan="2" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $index=>$category)
                        <tr class="{{$this->isColor($category->id)}}">

                            <td class="small-checkbox">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $category->id }}">
                            </td>
                            <td class="text-center">{{$index+$categories->firstItem()}}</td>
                            <td>{{ $category->name }}</td>
                            <!-- <td>{{ $category->slug }}</td> -->
                            <td class=" text-center "><a href=" {{ $category->getImage()}}" data-lightbox="example-1"><img src=" {{ $category->getImage()}}" alt="Slider Image" style="width:80px"></td>
                            <td>
                                <p class=" text-center {{$category->status==1?'bg-success':'bg-danger'}}">{{$category->status==1?'Bật':'Tắt'}}</p>
                            </td>
                            <td> <a href="#" class="btn-small d-block btn-sm btn-success" wire:click.prevent="showEditCategory({{$category->id}})">Sửa</a></td>
                            <td><a href="#" class="btn-small d-block  btn-sm btn-danger" wire:click.prevent="deleteConfirmation({{$category->id}})">Xóa</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-danger">Không có danh mục nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>