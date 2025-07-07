<div>
    <div class="card">
        <!--Form thêm slider -->
        <div wire:ignore.self class="modal fade" id="rafaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                                            <label>Tiêu đề đầu</label>
                                            <input name="order-id" placeholder="Nhập tiêu đề đầu..." type="text" class="square" wire:model="top_title" wire:keyup="generateSlug">
                                            @error('top_title') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10" hidden>
                                            <label>Slug</label>
                                            <input name="order-id" placeholder="Nhập slug..." type="text" class="square" wire:model="slug">
                                            @error('slug') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Tiêu đề chính</label>
                                            <input name="order-id" placeholder="Nhập tiêu đề chính..." type="text" class="square" wire:model="title">
                                            @error('title') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Tiêu đề phụ</label>
                                            <input name="order-id" placeholder="Nhập tiêu đề phụ..." type="text" class="square" wire:model="sub_title">
                                            @error('sub_title') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="input-style mb-10">
                                            <label>Link</label>
                                            <input name="order-id" placeholder="Nhập link..." type="text" class="square" wire:model="link">
                                            @error('link') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Giảm giá</label>
                                            <input name="order-id" placeholder="Nhập giảm giá..." type="text" class="square" wire:model="offer" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            @error('offer') <span class=" error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-style mb-10">
                                            <label>Ảnh</label>
                                            <input name="order-id" placeholder="Nhập Ảnh..." type="file" class="square" wire:model="image" id="{{$rand}}">

                                            @if ($image)
                                            Xem ảnh trước khi tải lên:
                                            <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail">

                                            @elseif(str_contains($new_image,'http'))
                                            Xem ảnh trước khi tải lên:
                                            <img src=" {{$new_image}}" alt="">

                                            @else
                                            Xem ảnh trước khi tải lên:
                                            <img src=" {{asset('admin/slider/'.$new_image)}}" alt="">
                                            @endif
                                            @error('image') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="input-style mb-10">
                                            <label>Ngày bắt đầu</label>
                                            <input type="date" id="datepicker-start" placeholder="DD/MM/YYYY" wire:model="start_date">
                                            @error('start_date') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="input-style mb-10">
                                            <label>Ngày kết thúc</label>
                                            <input type="date" id="datepicker-end" placeholder="DD/MM/YYYY" wire:model="end_date">
                                            @error('end_date') <span class="error text-danger">{{ $message }}</span> @enderror
                                        </div>


                                        <div class="status_option">
                                            <label>Trạng thái</label> <br>
                                            <div class="icheck-material-teal incheck-inline" style="display: inline-block; margin-right: 15px;">
                                                <input type="radio" id="someRadioId2" name="someGroupName" value="1" wire:model="status" />
                                                <label for="someRadioId2">Bật</label>
                                            </div>
                                            <div class="icheck-material-teal incheck-inline" style="display: inline-block;">
                                                <input type="radio" id="someRadioId1" name="someGroupName" value="0" wire:model="status" />
                                                <label for="someRadioId1">Tắt</label>
                                            </div>
                                        </div>
                                        @error('status') <span class="error text-danger">{{ $message }}</span> @enderror

                                    </div>
                                </form>
                            </div>

                        </form>

                    </div>
                    <div class="modal-footer">
                        @if($editForm)
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="resetForm">Close</button>
                        <button type="button" class="btn btn-primary" wire:click.prevent="updateSlider()">Cập nhật Slider</button>
                        @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="resetForm">Close</button>
                        <button type="button" class="btn btn-primary" wire:click.prevent="addSlider()">Thêm Slider</button>
                        @endif
                    </div>


                </div>
            </div>
        </div>




























        <div class="card-header">
            <!-- <h5 class="mb-0">Slider</h5> -->
            <!-- Lọc slider -->
            <div class="shop-product-fillter mb-0">
                <!-- Thêm slider -->
                <div style="display: flex; align-items: center; gap: 20px;">
                    <!-- Nút Thêm Slider -->
                    <a href="#" class="btn-sm btn-primary ml-3" wire:click.prevent="showSliderModal">Thêm Slider</a>

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
                <div class="sort-by-product-area align-items-center">
                    <!-- Lọc slider -->

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

                    <!-- Lọc slider -->
                    <div class="sort-by-cover">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps-sort"></i>Hiển thị:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span>{{ $pagesize }} <i class="fi-rs-angle-small-down"></i></span>
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
            <!-- Kết thúc lọc slider -->



























        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" wire:model.live="selectAll"></th>
                            <th>STT</th>
                            <th>Tiêu đề đầu</th>
                            <th>Link</th>
                            <th>Sale(%)</th>
                            <th>Ảnh</th>
                            <th>Trạng thái</th>
                            <th>Ngày kết thúc</th>
                            <th colspan="3" class="text-center">Thao tác</th> <!-- Tăng colspan thành 3 để chứa cột Xem chi tiết -->
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sliders as $index => $slider)
                        <tr class="{{$this->isColor($slider->id)}}">
                            <td>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $slider->id }}">
                            </td>
                            <td class="text-center">{{ $index + $sliders->firstItem() }}</td>
                            <td>{{ $slider->top_title }}</td>
                            <td>{{ $slider->link }}</td>
                            <td>{{ $slider->offer }} (%)</td>
                            <td>
                                <a href="{{ $slider->getImage() }}" data-lightbox="example-1">
                                    <img src="{{ $slider->getImage() }}" alt="Slider Image" style="width:80px">
                                </a>
                            </td>
                            <td>
                                <p class="text-center {{ $slider->status == 1 ? 'bg-success' : 'bg-danger' }}">{{ $slider->status == 1 ? 'Bật' : 'Tắt' }}</p>
                            </td>
                            <td>{{ $slider->end_date }}</td>
                            <td>
                                <a href="#" class="btn-small d-block btn-sm btn-success" wire:click.prevent="showEditSlider({{ $slider->id }})">Sửa</a>
                            </td>
                            <td>
                                <a href="#" class="btn-small d-block btn-sm btn-danger" wire:click.prevent="deleteConfirmation({{ $slider->id }})">Xóa</a>
                            </td>
                            <td>
                                <a href="#" class="btn-small d-block btn-sm btn-info" wire:click.prevent="showDetailSlider({{ $slider->id }})">
                                    <i class="bi bi-eye-fill me-1"></i>
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-danger">Không có slider nào</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
                {{ $sliders->links() }}
            </div>
        </div>
    </div>
    <!-- Modal Xem Chi Tiết Slider -->
    <div wire:ignore.self class="modal fade" id="detailSliderModal" tabindex="-1" aria-labelledby="detailSliderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailSliderModalLabel">Chi Tiết Slider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click.prevent="closeDetailModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Tiêu đề đầu:</strong></label>
                                <p>{{ $detail_top_title }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Slug:</strong></label>
                                <p>{{ $detail_slug }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Tiêu đề chính:</strong></label>
                                <p>{{ $detail_title }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Tiêu đề phụ:</strong></label>
                                <p>{{ $detail_sub_title }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Link:</strong></label>
                                <p>{{ $detail_link }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Giảm giá:</strong></label>
                                <p>{{ $detail_offer }}%</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Ảnh:</strong></label>
                                <div>
                                    @if($detail_image)
                                    <img src="{{ asset('admin/slider/' . $detail_image) }}" alt="Slider Image" class="img-thumbnail" style="max-width: 200px;">
                                    @else
                                    <p>Không có ảnh</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Ngày bắt đầu:</strong></label>
                                <p>{{ $detail_start_date }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Ngày kết thúc:</strong></label>
                                <p>{{ $detail_end_date }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Trạng thái:</strong></label>
                                <p class="{{ $detail_status == 1 ? 'text-success' : 'text-danger' }}">{{ $detail_status == 1 ? 'Bật' : 'Tắt' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="closeDetailModal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:init', function() {
        // Lắng nghe sự kiện show-detail-slider-modal
        Livewire.on('show-detail-slider-modal', () => {
            let modal = new bootstrap.Modal(document.getElementById('detailSliderModal'));
            modal.show();
        });

        // Lắng nghe sự kiện hide-detail-slider-modal
        Livewire.on('hide-detail-slider-modal', () => {
            let modal = bootstrap.Modal.getInstance(document.getElementById('detailSliderModal'));
            if (modal) {
                modal.hide();
            }
        });
    });
</script>