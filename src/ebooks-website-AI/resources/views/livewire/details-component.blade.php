<div>
    <style>
        .wishlisted {
            background-color: #f15412 !important;
            border: 1px solid transparent !important;
        }

        .wishlisted i {
            color: #fff !important;
        }

        .zoomContainer {
            z-index: 999 !important;
        }
    </style>

    <div wire:ignore class="modal fade custom-modal" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="row g-1">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="detail-info">
                                <h3 class="title-detail mt-30 pb-10"></h3>

                                <div class="clearfix product-price-cover">
                                    <div class="product-price primary-color float-left">
                                        <ins><span class="font-lg text-brand">Sản phẩm mới đã được thêm vào giỏ hàng của bạn</span></ins>
                                        <button class="button-42" role="button"><a href="{{ route('cart') }}" class="text-white"><b>Xem giỏ hàng</b></a></button>
                                        <style>
                                            .flex-container {
                                                display: flex;
                                                align-items: center;
                                                justify-content: space-between;
                                            }

                                            .button-42 {
                                                left: 150px;
                                                background-color: initial;
                                                background-image: linear-gradient(-180deg, #F15412, #F15412);
                                                border-radius: 6px;
                                                box-shadow: rgba(0, 0, 0, 0.1) 0 2px 4px;
                                                color: #FFFFFF;
                                                cursor: pointer;
                                                display: inline-block;
                                                font-family: Inter, -apple-system, system-ui, Roboto, "Helvetica Neue", Arial, sans-serif;
                                                height: 50px;
                                                line-height: 50px;
                                                outline: 0;
                                                overflow: hidden;
                                                padding: 0 20px;
                                                pointer-events: auto;
                                                position: relative;
                                                text-align: center;
                                                touch-action: manipulation;
                                                user-select: none;
                                                -webkit-user-select: none;
                                                vertical-align: top;
                                                white-space: nowrap;
                                                width: auto;
                                                z-index: 9;
                                                border: 0;
                                                transition: box-shadow .2s;
                                            }

                                            .button-42:hover {
                                                box-shadow: rgba(253, 76, 0, 0.5) 0 3px 8px;
                                            }
                                        </style>
                                    </div>
                                </div>
                                <div class="bt-1 border-color-1 mt-15 mb-15"></div>
                                <div class="short-desc mb-30">
                                    <main class="main">
                                        <section class="mt-30 mb-30">
                                            <div class="container">
                                                <div class="row g-1">
                                                    <div class="col-lg-12">
                                                        <h3 class="section-title mb-20"><span>ĐỀ XUẤT</span> MỚI</h3>
                                                        <div class="row product-grid-3 g-1">
                                                            @foreach($qproducts as $qproduct)
                                                            <div class="col-lg-3 col-md-3 col-6 col-sm-6">
                                                                <div class="product-cart-wrap mb-20">
                                                                    <div class="product-img-action-wrap">
                                                                        <div class="product-img product-img-zoom">
                                                                            <a href="{{route('details', ['slug'=>$qproduct->slug])}}">
                                                                                <img class="default-img" src="{{asset('admin/product/'.$qproduct->image)}}" alt="">
                                                                            </a>
                                                                        </div>
                                                                        <div class="product-badges product-badges-position product-badges-mrg">
                                                                            @if($qproduct->is_hot)
                                                                            <span class="hot">Hot</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="product-content-wrap">
                                                                        <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                                            <a href="{{route('details', ['slug'=>$qproduct->slug])}}">{{$qproduct->name}}</a>
                                                                        </h2>
                                                                        <div class="product-price" style="font-size: 12px;">
                                                                            @if($qproduct->sale_price && $qproduct->sale_price > 0 && $qproduct->sale_price < $qproduct->reguler_price)
                                                                                <span style="color: #e74c3c; font-weight: bold;">{{number_format($qproduct->sale_price, 3, ',', '.')}}đ</span>
                                                                                <span class="old-price" style="text-decoration: line-through; color: #999; margin-left: 5px;">{{number_format($qproduct->reguler_price, 3, ',', '.')}}đ</span>
                                                                                @else
                                                                                <span style="color: #333; font-weight: bold;">{{number_format($qproduct->reguler_price, 3, ',', '.')}}đ</span>
                                                                                @endif
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
                                    </main>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('home') }}" rel="nofollow">Trang chủ</a>
                <span></span> {{$product->category->name}}
                <span></span> {{$product->name}}
            </div>
        </div>
    </div>
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="product-detail accordion-detail">
                        <div class="row mb-50">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="detail-gallery" wire:ignore>
                                    <span class="zoom-icon"><i class="fi-rs-search"></i></span>
                                    <div class="product-image-slider">
                                        @foreach($images as $image)
                                        <figure class="border-radius-10">
                                            <img src="{{asset('admin/product/'.$image)}}" alt="product image">
                                        </figure>
                                        @endforeach
                                    </div>
                                    <div class="slider-nav-thumbnails pl-15 pr-15">
                                        @foreach(explode(',', $product->images) as $image)
                                        <div>
                                            <img src="{{ asset('admin/product/' . $image) }}" alt="product image">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="social-icons single-share">
                                    <ul class="text-grey-5 d-inline-block">
                                        <li class="social-facebook"><a href="#"><img src="assets/imgs/theme/icons/icon-facebook.svg" alt=""></a></li>
                                        <li class="social-twitter"><a href="#"><img src="assets/imgs/theme/icons/icon-twitter.svg" alt=""></a></li>
                                        <li class="social-instagram"><a href="#"><img src="assets/imgs/theme/icons/icon-instagram.svg" alt=""></a></li>
                                        <li class="social-linkedin"><a href="#"><img src="assets/imgs/theme/icons/icon-pinterest.svg" alt=""></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="detail-info">
                                    <h2 class="title-detail">{{$product->name}}</h2>
                                    <div class="product-detail-rating">
                                        <div class="pro-details-brand" style="color: #e74c3c; font-size: 14px;">
                                            <span>Đã bán: {{ number_format($soldQuantity, 0, ',', '.') }} sản phẩm</span>
                                        </div>
                                        <style>
                                            .star {
                                                line-height: 1;
                                                font-size: 12px;
                                            }
                                        </style>
                                        <div class=" product-rate-cover text-end">
                                            <div class="product-rate d-inline-block">
                                                @if($reviews->where('status', 'approved')->count() > 0)
                                                @php
                                                $averageRating = round($reviews->sum('rating') / $reviews->count(), 1);
                                                @endphp
                                                <div class="star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <=$averageRating)
                                                        <span class="star">★</span>
                                                        @else
                                                        <span class="star">☆</span>
                                                        @endif
                                                        @endfor
                                                </div>
                                                @else
                                                @endif
                                            </div>
                                            <span class="font-small ml-5 text-muted">
                                                {{ $reviews->where('status', 'approved')->count() }} đánh giá
                                            </span>
                                        </div>
                                    </div>
                                    <div class="clearfix product-price-cover">
                                        <div class="product-price primary-color float-left">
                                            <ins><span class="text-brand" style="font-size: 20px">{{number_format($product->sale_price, 3, ',', '.')}}đ</span></ins>
                                            <ins><span class="old-price font-md ml-15">{{number_format($product->reguler_price, 3, ',', '.')}}đ</span></ins>
                                            @php
                                            $loss = $product->reguler_price - $product->sale_price;
                                            $percent = ($loss / $product->reguler_price) * 100;
                                            @endphp
                                            <span class="save-price font-md color3 ml-15" style="background-color: red; color: white; font-weight: bold; padding: 5px 10px; border-radius: 4px; display: inline-block;">
                                                {{round($percent)}}%
                                            </span>
                                            <div class="button-arrow">
                                                <style>
                                                    .button-arrow {
                                                        color: #fff;
                                                        cursor: pointer;
                                                        display: inline-block;
                                                        margin: 10px;
                                                        padding: 8px;
                                                        position: relative;
                                                        width: auto;
                                                    }

                                                    .button-arrow:hover:after,
                                                    .button-arrow:hover:before {
                                                        box-shadow: 3px 2px 2px #22313f;
                                                    }

                                                    .button-arrow:active:after,
                                                    .button-arrow:active:before {
                                                        box-shadow: none;
                                                    }

                                                    .button-arrow:after,
                                                    .button-arrow:before {
                                                        background-color: #0077ba;
                                                        content: "\0020";
                                                        display: block;
                                                        height: 100%;
                                                        position: absolute;
                                                        top: 0;
                                                    }

                                                    .button-arrow:after {
                                                        left: 25%;
                                                        width: 75%;
                                                        transform: skewX(-30deg);
                                                        z-index: -1;
                                                    }

                                                    .button-arrow:before {
                                                        left: 0;
                                                        width: 50%;
                                                        z-index: -1;
                                                    }
                                                </style>
                                                @if($product->quantity)
                                                <span>Còn Hàng</span>
                                                @else
                                                <span>Hết Hàng</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bt-1 border-color-1 mt-15 mb-15"></div>
                                    <div class="short-desc mb-30">
                                    </div>
                                    <div class="product_sort_info font-xs mb-30">
                                        <ul>
                                            <li class="mb-10">Nhà xuất bản: {{$product->publisher}}</li>
                                            <li class="mb-10">Tác giả: {{$product->author}}</li>
                                            <li>Độ tuổi: {{$product->age}}</li>
                                        </ul>
                                    </div>
                                    <div class="offers-container">
                                        <div class="offers-header">
                                            <h2 class="offers-title">Ưu đãi liên quan</h2>
                                            <a href="#" class="view-more" wire:click.prevent="toggleShowAll">
                                                Xem tất cả mã giảm giá
                                            </a>
                                        </div>
                                        @if($showAllCoupons)
                                        <div class="modal show" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tất cả mã giảm giá</h5>
                                                        <button type="button" class="btn-close" wire:click="toggleShowAll"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="coupons-list">
                                                            @foreach($coupons as $coupon)
                                                            <div class="offer-card" wire:click="showCouponDetails({{ $coupon->id }})">
                                                                <div class="offer-icon discount">
                                                                    <span class="percent">%</span>
                                                                </div>
                                                                <div class="offer-content">
                                                                    @if($coupon->coupon_type === 'fixed')
                                                                    <h3>Mã giảm {{ number_format($coupon->coupon_value, 3, '.', '') }}k - tất cả đơn hàng</h3>
                                                                    @elseif($coupon->coupon_type === 'percent')
                                                                    <h3>Mã giảm {{ number_format($coupon->coupon_value, 3, '.', '') }}% - tất cả đơn hàng</h3>
                                                                    @else
                                                                    <h3>Mã giảm giá không xác định</h3>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" wire:click="toggleShowAll">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($selectedCoupon)
                                        <div class="modal show" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Chi tiết mã giảm giá</h5>
                                                        <button type="button" class="btn-close" wire:click="closeCouponDetails"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Mã giảm:</strong> {{ $selectedCoupon->coupon_code }}</p>
                                                        <p><strong>Giảm giá:</strong>
                                                            @if($selectedCoupon->coupon_type === 'fixed')
                                                            {{ number_format($selectedCoupon->coupon_value, 3, '.', '') }}k
                                                            @elseif($selectedCoupon->coupon_type === 'percent')
                                                            {{ number_format($selectedCoupon->coupon_value, 3, '.', '') }}%
                                                            @endif
                                                        </p>
                                                        <p><strong>Áp dụng cho giá trị giỏ hàng từ:</strong> {{ number_format($selectedCoupon->cart_value, 0, '.', '') }}k</p>
                                                        <p><strong>Ngày hết hạn:</strong> {{ $selectedCoupon->end_date }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" wire:click="closeCouponDetails">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="offers-scroll" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($coupons as $coupon)
                                            @if($showAllCoupons || $loop->index < 5)
                                                <div class="offer-card" wire:click="showCouponDetails({{ $coupon->id }})">
                                                <div class="offer-icon discount">
                                                    <span class="percent">%</span>
                                                </div>
                                                <div class="offer-content">
                                                    @if($coupon->coupon_type === 'fixed')
                                                    <h3>Mã giảm {{ number_format($coupon->coupon_value, 3, '.', '') }}k - tất cả đơn hàng</h3>
                                                    @elseif($coupon->coupon_type === 'percent')
                                                    <h3>Mã giảm {{ number_format($coupon->coupon_value, 3, '.', '') }}% - tất cả đơn hàng</h3>
                                                    @else
                                                    <h3>Mã giảm giá không xác định</h3>
                                                    @endif
                                                </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    @if($selectedCoupon)
                                    <div class="modal show" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Chi tiết mã giảm giá</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Mã giảm:</strong> {{ $selectedCoupon->coupon_code }}</p>
                                                    <p><strong>Giảm giá:</strong>
                                                        @if($selectedCoupon->coupon_type === 'fixed')
                                                        {{ number_format($selectedCoupon->coupon_value, 0, '.', '') }}k
                                                        @elseif($selectedCoupon->coupon_type === 'percent')
                                                        {{ number_format($selectedCoupon->coupon_value, 0, '.', '') }}%
                                                        @endif
                                                    </p>
                                                    <p><strong>Áp dụng cho giá trị giỏ hàng từ:</strong> {{ number_format($selectedCoupon->cart_value, 3, '.', '') }}k</p>
                                                    <p><strong>Ngày hết hạn:</strong> {{ $selectedCoupon->end_date }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" wire:click="closeCouponDetails">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <style>
                                        .modal {
                                            position: fixed;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            background-color: rgba(0, 0, 0, 0.5);
                                            display: none;
                                            justify-content: center;
                                            align-items: center;
                                            z-index: 1050;
                                        }

                                        .modal.show {
                                            display: flex;
                                        }

                                        .modal-dialog {
                                            background-color: #fff;
                                            padding: 20px;
                                            border-radius: 8px;
                                            max-width: 500px;
                                            width: 100%;
                                        }

                                        .offers-container {
                                            padding: 16px;
                                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                                        }

                                        .offers-header {
                                            display: flex;
                                            justify-content: space-between;
                                            align-items: center;
                                            margin-bottom: 16px;
                                        }

                                        .offers-title {
                                            font-size: 20px;
                                            font-weight: 600;
                                            margin: 0;
                                            color: #333;
                                        }

                                        .view-more {
                                            color: #1890ff;
                                            text-decoration: none;
                                            font-size: 14px;
                                        }

                                        .offers-scroll {
                                            display: flex;
                                            gap: 12px;
                                            overflow-x: auto;
                                            padding: 4px;
                                            scrollbar-width: none;
                                            -ms-overflow-style: none;
                                        }

                                        .offers-scroll::-webkit-scrollbar {
                                            display: none;
                                        }

                                        .offer-card {
                                            min-width: 200px;
                                            padding: 12px;
                                            border: 1px solid #e8e8e8;
                                            border-radius: 8px;
                                            display: flex;
                                            align-items: center;
                                            gap: 12px;
                                            background: white;
                                            cursor: pointer;
                                            transition: transform 0.2s;
                                        }

                                        .offer-card:hover {
                                            transform: translateY(-2px);
                                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                        }

                                        .offer-icon {
                                            width: 40px;
                                            height: 40px;
                                            border-radius: 8px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        }

                                        .offer-icon.discount {
                                            background-color: #ffd60a;
                                        }

                                        .offer-icon.credit {
                                            background-color: #e6f4ff;
                                        }

                                        .percent {
                                            color: #fff;
                                            font-weight: bold;
                                            font-size: 18px;
                                        }

                                        .offer-content h3 {
                                            margin: 0;
                                            font-size: 14px;
                                            font-weight: 500;
                                            color: #333;
                                        }

                                        .offer-icon img {
                                            width: 24px;
                                            height: 24px;
                                        }

                                        @media (max-width: 768px) {
                                            .offer-card {
                                                min-width: 180px;
                                            }

                                            .offers-title {
                                                font-size: 18px;
                                            }
                                        }
                                    </style>
                                </div>
                                <div class="detail-extralink">
                                    <div class="detail-qty border radius">
                                        <a href="#" class="qty-down" wire:click.prevent="QtyDecrease()">
                                            <i class="fi-rs-angle-small-down"></i>
                                        </a>
                                        <span class="qty-val">{{ $qty }}</span>
                                        <a href="#" class="qty-up" wire:click.prevent="QtyIncrease({{ $product->quantity }})">
                                            <i class="fi-rs-angle-small-up"></i>
                                        </a>
                                    </div>
                                    <div class="product-extra-link2">
                                        @if(session()->has('admin_alert'))
                                        <div class="alert alert-warning">
                                            {{ session('admin_alert') }}
                                        </div>
                                        @endif
                                        @if($product->quantity == 0)
                                        <button type="submit" class="button button-add-to-cart" disabled data-bs-toggle="modal" data-bs-target="#quickViewModal">
                                            <b>Thêm vào giỏ hàng</b>
                                        </button>
                                        @else
                                        @if(Auth::check() && Auth::user()->utype === 'admin')
                                        <button type="button" class="button button-add-to-cart" disabled wire:click.prevent="showAdminAlert">
                                            <b>Thêm vào giỏ hàng</b>
                                        </button>
                                        @else
                                        <button type="submit" class="button button-add-to-cart" data-bs-toggle="modal" data-bs-target="#quickViewModal" wire:click.prevent="Store({{$product->id}},'{{$product->name}}',{{$product->sale_price}})">
                                            <b>Thêm vào giỏ hàng</b>
                                        </button>
                                        @endif
                                        @endif
                                        @php
                                        $witem = Cart::instance('wishlist')->content()->pluck('id');
                                        @endphp
                                        @if($witem->contains($product->id))
                                        <a aria-label="Remove From Wishlist" class="action-btn hover-up wishlisted" href="{{route('wishlist')}}" wire:click.prevent="removefromWishlist({{$product->id}})">
                                            <i class="fi-rs-heart"></i>
                                        </a>
                                        @else
                                        <a aria-label="Add To Wishlist" class="action-btn hover-up" href="{{route('wishlist')}}" wire:click.prevent="addtoWishlist({{$product->id}},'{{$product->name}}',{{$product->sale_price}})">
                                            <i class="fi-rs-heart"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .tab-style3 {
                            margin-top: -80px;
                        }
                    </style>
                    <div class="tab-style3">
                        <ul class="nav nav-tabs text-uppercase">
                            <li class="nav-item">
                                <a class="nav-link active" id="Description-tab" data-bs-toggle="tab" href="#Description">Thông tin sản phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="Additional-info-tab" data-bs-toggle="tab" href="#Additional-info">Mô tả</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="Reviews-tab" data-bs-toggle="tab" href="#Reviews">
                                    Đánh giá ({{ $reviews->where('status', 'approved')->count() ?? 0 }})
                                </a>
                            </li>
                        </ul>
                        <style>
                            .star-rating {
                                display: flex;
                                gap: 2px;
                                font-size: 18px;
                                color: #ffc107;
                                line-height: 1;
                            }

                            .star {
                                line-height: 1;
                            }

                            .single-comment h6 {
                                font-size: 16px;
                                font-weight: 600;
                            }

                            .single-comment p {
                                margin-top: 5px;
                                font-size: 14px;
                                color: #6c757d;
                            }

                            .text-muted {
                                font-size: 12px;
                            }

                            /* Thêm style mới để điều chỉnh khoảng cách */
                            .tab-content .tab-pane {
                                padding: 20px;
                                /* Thêm padding cho các tab để tạo khoảng cách */
                            }

                            .tab-content .tab-pane#Description p,
                            .tab-content .tab-pane#Additional-info p {
                                margin: 0;
                                /* Xóa margin mặc định của thẻ p */
                                padding: 10px 0;
                                /* Thêm padding trên dưới để tạo khoảng cách */
                                line-height: 1.6;
                                /* Tăng khoảng cách dòng để dễ đọc hơn */
                            }

                            .nav-tabs {
                                margin-bottom: 20px;
                                /* Tăng khoảng cách giữa tiêu đề tab và nội dung */
                                border-bottom: 2px solid #e8e8e8;
                                /* Đường viền dưới tab rõ hơn */
                            }

                            .nav-tabs .nav-link {
                                padding: 10px 20px;
                                /* Tăng padding cho các tab để chúng không sát nhau */
                                margin-right: 5px;
                                /* Khoảng cách giữa các tab */
                                border-radius: 5px 5px 0 0;
                                /* Bo góc trên của tab */
                            }

                            .comments-area h4 {
                                margin-bottom: 20px;
                                /* Giảm nhẹ margin-bottom của tiêu đề trong tab Đánh giá */
                            }

                            .comment-list {
                                margin-top: 10px;
                                /* Thêm khoảng cách trên cho danh sách bình luận */
                            }
                        </style>
                        <div class="tab-content shop_info_tab entry-main-content">
                            <div class="tab-pane fade show active" id="Description">
                                <p>{{ $product->short_description }}</p>
                            </div>
                            <div class="tab-pane fade" id="Additional-info">
                                <p>{{ $product->long_description }}</p>
                            </div>
                            <div class="tab-pane fade" id="Reviews">
                                <div class="comments-area">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <!-- <h4 class="mb-30">Đánh giá sản phẩm</h4> -->
                                            <div class="comment-list">
                                                @if($reviews->isEmpty())
                                                <p class="text-center text-muted">Chưa có đánh giá nào</p>
                                                @else
                                                @foreach($reviews as $review)
                                                @if($review->status === 'approved')
                                                <div class="single-comment d-flex mb-4 p-3 shadow-sm rounded bg-white">
                                                    <div class="desc flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <h6 class="mb-0 me-2 text-dark">
                                                                <a href="#" class="text-decoration-none">
                                                                    {{ $review->user ? $review->user->name : 'Người dùng không xác định' }}
                                                                </a>
                                                            </h6>
                                                            <div class="star-rating">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <=$review->rating)
                                                                    <span class="star">★</span>
                                                                    @else
                                                                    <span class="star">☆</span>
                                                                    @endif
                                                                    @endfor
                                                            </div>
                                                        </div>
                                                        <p class="mb-2 text-secondary">{{ $review->comment }}</p>
                                                        <div class="review-images mb-2 d-flex gap-2">
                                                            @if($review->images)
                                                            @foreach(explode(',', $review->images) as $image)
                                                            <img src="{{ asset('admin/review/' . $image) }}"
                                                                alt="Ảnh đánh giá"
                                                                class="img-fluid rounded"
                                                                style="width: 100px; height: 100px; object-fit: cover;">
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-muted font-xs">
                                                                {{ $review->created_at->format('d/m/Y H:i') }}
                                                            </span>
                                                        </div>
                                                        @if($review->admin_reply)
                                                        <div class="admin-reply mt-3 bg-light p-3 rounded">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <span class="badge bg-primary me-2">Panda.com</span>
                                                                <small class="text-muted">Phản hồi:</small>
                                                            </div>
                                                            <p class="mb-1">{{ $review->admin_reply }}</p>
                                                            <small class="text-muted">
                                                                {{ $review->admin_reply_at ? $review->admin_reply_at->format('d/m/Y H:i') : '' }}
                                                            </small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                                @endforeach
                                                @if($reviews->where('status', 'approved')->isEmpty())
                                                <p class="text-center text-muted">Chưa có đánh giá nào được xác nhận</p>
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <h4 class="mb-30">Đánh giá ({{ $reviews->where('status', 'approved')->count() ?? 0 }})</h4>
                                            <div class="d-flex mb-30">
                                                <div class="product-rate d-inline-block mr-15">
                                                    <div class="product-rating" style="width: {{ $reviews->count() > 0 ? ($reviews->sum('rating') / ($reviews->count() * 5)) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <h6>{{ $reviews->count() > 0 ? round($reviews->sum('rating') / $reviews->count(), 1) : 0 }}/5 sao</h6>
                                            </div>
                                            @php
                                            $totalReviews = $reviews->count();
                                            $ratingStats = [
                                            '5_sao' => $reviews->where('rating', 5)->count(),
                                            '4_sao' => $reviews->where('rating', 4)->count(),
                                            '3_sao' => $reviews->where('rating', 3)->count(),
                                            '2_sao' => $reviews->where('rating', 2)->count(),
                                            '1_sao' => $reviews->where('rating', 1)->count(),
                                            ];
                                            @endphp
                                            @foreach($ratingStats as $star => $count)
                                            <div class="progress">
                                                <span>{{ ucfirst(str_replace('_', ' ', $star)) }}</span>
                                                <div class="progress-bar"
                                                    role="progressbar"
                                                    style="width: {{ $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0 }}%;"
                                                    aria-valuenow="{{ $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0 }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0 }}%
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <style>
                        .related-products .col-lg-3 {
                            padding-left: 15px;
                            padding-right: 15px;
                            margin-bottom: 30px;
                        }
                    </style>

                    <div class="row related-products g-0"> 
                        <div class="col-12">
                            <h3 class="section-title style-1 mb-30 wow fadeIn animated">Sản phẩm liên quan</h3>
                        </div>
                        @foreach($rproducts as $rproduct)
                        <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                            <div class="product-cart-wrap small hover-up">
                                <div class="product-img-action-wrap">
                                    <div class="product-img product-img-zoom">
                                        <a href="{{route('details', ['slug'=>$rproduct->slug])}}" tabindex="0">
                                            <img class="default-img img-thumbnail" src="{{asset('admin/product/'.$rproduct->image)}}" alt="">
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap">
                                    <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                        <a href="{{route('details', ['slug'=>$rproduct->slug])}}">{{$rproduct->name}}</a>
                                    </h2>
                                    <div class="product-badges product-badges-position product-badges-mrg">
                                        @if($rproduct->is_hot)
                                        <span class="hot">Hot</span>
                                        @endif
                                        @if($rproduct->sale_price && $rproduct->sale_price > 0 && $rproduct->sale_price < $rproduct->reguler_price && $rproduct->reguler_price > 0)
                                            <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                                - {{ round(($rproduct->reguler_price - $rproduct->sale_price) / $rproduct->reguler_price * 100) }}%
                                            </span>
                                            @endif
                                    </div>
                                    <div class="product-sold" style="margin: 5px 0; font-size: 12px; color:rgb(234, 87, 50);">
                                        <span>Đã bán: {{ number_format($rproduct->sold_quantity ?? 0, 0, ',', '.') }} sản phẩm</span>
                                    </div>
                                    <div class="product-price" style="font-size: 12px;">
                                        @if($rproduct->sale_price && $rproduct->sale_price > 0 && $rproduct->sale_price < $rproduct->reguler_price)
                                            <span style="color: #e74c3c; font-weight: bold;">{{number_format($rproduct->sale_price, 3, ',', '.')}}đ</span>
                                            <span class="old-price" style="text-decoration: line-through; color: #999; margin-left: 5px;">{{number_format($rproduct->reguler_price, 3, ',', '.')}}đ</span>
                                            @else
                                            <span style="color: #333; font-weight: bold;">{{number_format($rproduct->reguler_price, 3, ',', '.')}}đ</span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-3 primary-sidebar sticky-sidebar" wire:ignore>
                <div class="widget-category mb-30">
                    <h5 class="section-title style-1 mb-30 wow fadeIn animated">Danh Mục Sản phẩm</h5>
                    <ul class="categories">
                        <li><a href="{{ route('shop') }}">TẤT CẢ DANH MỤC</a></li>
                        @foreach ($categories as $category)
                        <li><a href="{{route('product.category', ['slug'=>$category->slug])}}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="sidebar-widget product-sidebar mb-30 p-30 bg-grey border-radius-10">
                    <div class="widget-header position-relative mb-20 pb-10">
                        <h5 class="widget-title mb-10">Sản Phẩm Mới</h5>
                        <div class="bt-1 border-color-1"></div>
                    </div>
                    @foreach($nproducts as $nproduct)
                    <div class="single-post clearfix">
                        <div class="image">
                            <img src="{{asset('admin/product/'.$nproduct->image)}}" alt="#">
                        </div>
                        <div class="content pt-10">
                            <h5 style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <a href="{{route('details', ['slug'=>$nproduct->slug])}}">{{$nproduct->name}}</a>
                            </h5>
                            <p class="price mb-0 mt-5" style="font-size: 14px;">
                                @if($nproduct->sale_price && $nproduct->sale_price > 0 && $nproduct->sale_price < $nproduct->reguler_price)
                                    <span style="color: #e74c3c; font-weight: bold;">{{number_format($nproduct->sale_price, 3, ',', '.')}}đ</span>
                                    <span style="text-decoration: line-through; color: #999; font-size: 12px; margin-left: 5px;">{{number_format($nproduct->reguler_price, 3, ',', '.')}}đ</span>
                                    @else
                                    <span style="color: #333; font-weight: bold;">{{number_format($nproduct->reguler_price, 3, ',', '.')}}đ</span>
                                    @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleReplyForm(reviewId) {
            const form = document.getElementById(`replyForm-${reviewId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</div>