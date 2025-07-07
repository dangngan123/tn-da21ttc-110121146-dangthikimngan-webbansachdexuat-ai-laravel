<div>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}" rel="nofollow">Trang chủ</a>
                    <span></span> Shop
                </div>
            </div>
        </div>
        <section class="mt-50 mb-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="shop-product-fillter">
                            <div class="totall-product">
                                <p><strong class="text-brand">{{ $products->total() }}</strong> sản phẩm: <strong>{{ $cateroryName }}</strong></p>
                            </div>
                            <div class="sort-by-product-area">
                                <div class="sort-by-cover mr-10">
                                    <div class="sort-by-product-wrap">
                                        <div class="sort-by">
                                            <span><i class="fi-rs-apps"></i>Hiện:</span>
                                        </div>
                                        <div class="sort-by-dropdown-wrap">
                                            <span> {{$pagesize}} <i class="fi-rs-angle-small-down"></i></span>
                                        </div>
                                    </div>
                                    <div class="sort-by-dropdown">
                                        <ul>
                                            <li><a class="{{ $pagesize == 12 ? 'active' : '' }}" href="#" wire:click="changepageSize(12)">12</a></li>
                                            <li><a class="{{ $pagesize == 24 ? 'active' : '' }}" href="#" wire:click="changepageSize(24)">24</a></li>
                                            <li><a class="{{ $pagesize == 36 ? 'active' : '' }}" href="#" wire:click="changepageSize(36)">36</a></li>
                                            <li><a class="{{ $pagesize == 48 ? 'active' : '' }}" href="#" wire:click="changepageSize(48)">48</a></li>
                                            <li><a class="{{ $pagesize == 50 ? 'active' : '' }}" href="#" wire:click="changepageSize(50)">50</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="sort-by-cover">
                                    <div class="sort-by-product-wrap">
                                        <div class="sort-by">
                                            <span><i class="fi-rs-apps-sort"></i>Sắp xếp theo:</span>
                                        </div>
                                        <div class="sort-by-dropdown-wrap">
                                            <span> {{$orderBy}} <i class="fi-rs-angle-small-down"></i></span>
                                        </div>
                                    </div>
                                    <div class="sort-by-dropdown">
                                        <ul>
                                            <li><a class="{{ $orderBy == 'Mặc định' ? 'active' : '' }}" href="#" wire:click="changeOrderBy('Mặc định')">Mặc định</a></li>
                                            <li><a class="{{ $orderBy == 'Giá thấp' ? 'active' : '' }}" href="#" wire:click="changeOrderBy('Giá thấp')">Giá thấp</a></li>
                                            <li><a class="{{ $orderBy == 'Giá cao' ? 'active' : '' }}" href="#" wire:click="changeOrderBy('Giá cao')">Giá cao</a></li>
                                            <li><a class="{{ $orderBy == 'Sản phẩm mới' ? 'active' : '' }}" href="#" wire:click="changeOrderBy('Sản phẩm mới')">Sản phẩm mới</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row product-grid-3">
                            @foreach($products as $product)
                            <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                <div class="product-cart-wrap small hover-up">
                                    <div class="product-img-action-wrap">
                                        <div class="product-img product-img-zoom">
                                            <a href="{{route('details', ['slug'=>$product->slug])}}" tabindex="0">
                                                <img class="default-img img-thumbnail" src="{{asset('admin/product/'.$product->image)}}" alt="">
                                            </a>
                                        </div>
                                        <div class="product-badges product-badges-position product-badges-mrg">
                                            @if($product->is_hot)
                                            <span class="hot">Hot</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="product-content-wrap">
                                        <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="{{route('details', ['slug'=>$product->slug])}}">{{$product->name}}</a>
                                        </h2>
                                        <div class="product-badges product-badges-position product-badges-mrg">
                                            @if($product->is_hot)
                                            <span class="hot">Hot</span>
                                            @endif
                                            @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->reguler_price && $product->reguler_price > 0)
                                                <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                                    - {{ round(($product->reguler_price - $product->sale_price) / $product->reguler_price * 100) }}%
                                                </span>
                                            @endif
                                        </div>
                                        <div class="product-sold" style="margin: 5px 0; font-size: 12px; color:rgb(234, 87, 50);">
                                            <span>Đã bán: {{ number_format($product->sold_quantity ?? 0, 0, ',', '.') }} sản phẩm</span>
                                        </div>
                                        <div class="product-price">
                                            @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->reguler_price)
                                                <span style="color: #e74c3c; font-weight: bold;">{{ number_format($product->sale_price, 3, ',', '.') }}đ</span>
                                                <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 12px;">{{ number_format($product->reguler_price, 3, ',', '.') }}đ</span>
                                            @else
                                                <span style="color: #333; font-weight: bold;">{{ number_format($product->reguler_price, 0, ',', '.') }}đ</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="pagination-area mt-15 mb-sm-5 mb-lg-0">
                            {{ $products->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    <div class="col-lg-3 primary-sidebar sticky-sidebar">
                        <div class="widget-category mb-30">
                            <h5 class="section-title style-1 mb-30 wow fadeIn animated">Danh Mục Sản phẩm</h5>
                            <ul class="categories">
                                <li><a href="{{ route('shop') }}">TẤT CẢ DANH MỤC</a></li>
                                @foreach ($categories as $category)
                                <li><a href="{{route('product.category', ['slug'=>$category->slug])}}">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="sidebar-widget price_range range mb-30">
                            <div class="list-group">
                                <div class="list-group-item mb-10 mt-10">
                                    <label class="section-title" style="font-weight: bold; font-size: 16px;">Độ tuổi</label>
                                    <div class="custome-checkbox">
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="ageCheckbox1" wire:model.live="selectedAges" value="1-6">
                                        <label class="form-check-label" for="ageCheckbox1"><span>1 - 6 tuổi</span></label>
                                        <br>
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="ageCheckbox2" wire:model.live="selectedAges" value="7-13">
                                        <label class="form-check-label" for="ageCheckbox2"><span>7 - 13 tuổi</span></label>
                                        <br>
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="ageCheckbox3" wire:model.live="selectedAges" value="14-18">
                                        <label class="form-check-label" for="ageCheckbox3"><span>14 - 18 tuổi</span></label>
                                    </div>

                                    <label class="fw-900">Giá</label>
                                    <div class="custome-checkbox">
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="priceRange1" wire:model.live="priceRange" value="0-150">
                                        <label class="form-check-label" for="priceRange1"><span>0 đ - 150.000 đ</span></label>
                                        <br>
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="priceRange2" wire:model.live="priceRange" value="150-300">
                                        <label class="form-check-label" for="priceRange2"><span>150.000 đ - 300.000 đ</span></label>
                                        <br>
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="priceRange3" wire:model.live="priceRange" value="300-500">
                                        <label class="form-check-label" for="priceRange3"><span>300.000 đ - 500.000 đ</span></label>
                                        <br>
                                        <input class="form-check-input" type="checkbox" name="checkbox" id="priceRange4" wire:model.live="priceRange" value="500-700">
                                        <label class="form-check-label" for="priceRange4"><span>500.000 đ - 700.000 đ</span></label>
                                    </div>

                                    <label class="fw-900">Nhà xuất bản</label>
                                    <div class="custome-checkbox" wire:key="publishers-{{ $showAllPublishers ? 'all' : 'limited' }}">
                                        @foreach ($publishers as $publisher)
                                        @if($showAllPublishers || $loop->index < 8)
                                            <input class="form-check-input" type="checkbox" name="publisherCheckbox" id="publisherCheckbox{{ $loop->index }}" wire:model.live="selectedPublishers" value="{{ $publisher }}">
                                            <label class="form-check-label" for="publisherCheckbox{{ $loop->index }}"><span>{{ $publisher }}</span></label>
                                            <br>
                                        @endif
                                        @endforeach
                                        @if(!$showAllPublishers && $hasMorePublishers)
                                        <button wire:click="expandPublisherList" class="text-primary" style="font-size: 14px; background: none; border: none; padding: 0;">Xem thêm</button>
                                        @elseif($showAllPublishers)
                                        <button wire:click="collapsePublisherList" class="text-primary" style="font-size: 14px; background: none; border: none; padding: 0;">Thu gọn</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-widget product-sidebar mb-30 p-30 bg-grey border-radius-10">
                            <div class="widget-header position-relative mb-20 pb-10">
                                <h5 class="widget-title mb-10">Sản phẩm Mới</h5>
                                <div class="bt-1 border-color-1"></div>
                            </div>
                            @foreach($nproducts as $nproduct)
                            <div class="single-post clearfix">
                                <div class="image">
                                    <img src="{{asset('admin/product/'.$nproduct->image)}}" alt="#">
                                </div>
                                <div class="content pt-10">
                                    <h5 style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        <a href="{{ route('details', ['slug' => $nproduct->slug]) }}" style="color: black; text-decoration: none;">
                                            {{ $nproduct->name }}
                                        </a>
                                    </h5>
                                    <p class="price mb-0 mt-5" style="font-size: 14px;">
                                        @if($nproduct->sale_price && $nproduct->sale_price > 0 && $nproduct->sale_price < $nproduct->reguler_price)
                                            <div style="display: flex; align-items: center; gap: 5px; flex-wrap: nowrap;">
                                                <span style="color: #e74c3c; font-weight: bold;">{{ number_format($nproduct->sale_price, 3, ',', '.') }}đ</span>
                                                @if($nproduct->reguler_price > 0)
                                                <div class="discount-badge" style="font-size: 10px; color: #fff; background: #e74c3c; display: inline-block; padding: 2px 5px; border-radius: 3px; white-space: nowrap;">
                                                    - {{ round(($nproduct->reguler_price - $nproduct->sale_price) / $nproduct->reguler_price * 100) }}%
                                                </div>
                                                @else
                                                <span style="display: none;"></span>
                                                @endif
                                            </div>
                                            <div style="margin-top: 5px;">
                                                <span style="text-decoration: line-through; color: #999; font-size: 12px;">{{ number_format($nproduct->reguler_price, 3, ',', '.') }}đ</span>
                                            </div>
                                            @else
                                            <span style="color: #333; font-weight: bold;">{{ number_format($nproduct->reguler_price, 3, ',', '.') }}đ</span>
                                            @endif
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>