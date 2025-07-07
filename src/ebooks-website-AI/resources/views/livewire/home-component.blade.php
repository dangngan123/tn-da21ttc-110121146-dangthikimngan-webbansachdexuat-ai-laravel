<div>
    <main class="main">
        <style>
            .hero-slider-1 {
                position: relative;
                overflow: hidden;
                height: 400px;
            }

            .hero-slider-1 .single-hero-slider {
                height: 100%;
                position: absolute;
                width: 100%;
                top: 0;
                left: 0;
                opacity: 0;
                transition: all 0.5s ease-in-out;
                z-index: 0;
            }

            .hero-slider-1 .single-hero-slider.active {
                opacity: 1;
                z-index: 1;
                position: relative;
            }
        </style>

        <section class="home-slider" wire:ignore>
            <div class="hero-slider-1 dot-style-1 dot-style-1-position-1">
                @forelse($sliders as $slider)
                <div class="single-hero-slider single-animation-wrap"
                    style="background: linear-gradient(135deg, #c12530 0%, #e91e63 50%, #ff7043 100%);">
                    <div class="container">
                        <div class="row align-items-center justify-content-center slider-animated-1">
                            <div class="col-lg-5 col-md-6 d-flex flex-column justify-content-center align-items-center">
                                <div class="hero-slider-content-2 text-center" style="margin-top: -80px;">
                                    <div class="circle-text">
                                        <h3 class="animated" style="font-size: 30px; color: #fffbe6; font-weight: 800; text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.6);">
                                            🏖️ {{$slider->top_title}} 🌴
                                        </h3>
                                        <h4 class="animated fw-bold" style="font-size: 30px; color: #ffe082; font-weight: 700; margin: 15px 0; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);">
                                            ☀️ {{$slider->title}} ☀️
                                        </h4>
                                        <h4 class="animated" style="font-size: 30px; color:rgb(245, 232, 207); font-weight: 700; margin: 15px 0; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);">
                                            🛒 {{$slider->sub_title}} 🛒
                                        </h4>
                                        <h4 class="animated" style="font-size: 33px; color: #ffffff; font-weight: 900; margin: 20px 0; text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.6);">
                                            🎉 Tiết kiệm đến {{$slider->offer}}% 🎉
                                        </h4>
                                        <a class="animated btn explore-btn" href="{{$slider->link}}"
                                            style="background: linear-gradient(to right, #ff3d00, #ff1744); color: #fff; padding: 15px 30px; font-size: 20px; border-radius: 30px; transition: all 0.3s ease; box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);">
                                            👉 Khám Phá Ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="single-slider-img single-slider-img-1">
                                    <img class="animated slider-1-2" src="{{asset('admin/slider/'.$slider->image)}}" alt="{{$slider->top_title}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="single-hero-slider single-animation-wrap" style="background: linear-gradient(135deg, #ffcccb 0%, #ffd8b1 100%);">
                    <div class="container">
                        <div class="row align-items-center justify-content-center slider-animated-1">
                            <div class="col-12 text-center">
                                <h3 style="font-size: 24px; color: #666;">Chưa có slider nào được thiết lập.</h3>
                            </div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <div class="slider-arrow hero-slider-1-arrow"></div>
        </section>

        <section class="featured section-padding position-relative" wire:ignore>
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-4 mb-md-3 mb-lg-0">
                        <div class="banner-features wow fadeIn animated hover-up">
                            <img src="assets/imgs/theme/icons/feature-1.png" alt="">
                            <h4 class=" bg-1">Free Shipping</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-md-3 mb-lg-0">
                        <div class="banner-features wow fadeIn animated hover-up">
                            <img src="assets/imgs/theme/icons/feature-2.png" alt="">
                            <h4 class="bg-3">Online Order</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-md-3 mb-lg-0">
                        <div class="banner-features wow fadeIn animated hover-up">
                            <img src="assets/imgs/theme/icons/feature-3.png" alt="">
                            <h4 class="bg-2">Save Money</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-md-3 mb-lg-0">
                        <div class="banner-features wow fadeIn animated hover-up">
                            <img src="assets/imgs/theme/icons/feature-4.png" alt="">
                            <h4 class="bg-4">Promotions</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-md-3 mb-lg-0">
                        <div class="banner-features wow fadeIn animated hover-up">
                            <img src="assets/imgs/theme/icons/feature-5.png" alt="">
                            <h4 class="bg-5">Happy Sell</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-md-3 mb-lg-0">
                        <div class="banner-features wow fadeIn animated hover-up">
                            <img src="assets/imgs/theme/icons/feature-6.png" alt="">
                            <h4 class="bg-6">24/7 Support</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if($saletimer && $saletimerproducts->count() > 0 && $saletimer->status == 1 && $saletimer->start_date instanceof \Carbon\Carbon && $saletimer->sale_timer instanceof \Carbon\Carbon && $saletimer->start_date->lte(Carbon\Carbon::now()) && $saletimer->sale_timer->gt(Carbon\Carbon::now()))
        <div class="container">
            <div class="flash-sale-wrapper" style="background-color:rgb(245, 59, 59); padding: 20px 15px; border-radius: 10px; margin-bottom: 20px;">
                <div class="flash-sale-section" style="margin-top: -10px;">
                    <div class="flash-sale-header">
                        <div class="flash-sale-text">
                            <span>FLA</span>
                            <i class="fas fa-bolt flash-icon"></i>
                            <span>SALE</span>
                        </div>
                        <div class="countdown-wrapper">
                            <span>Kết thúc trong</span>
                            <div class="countdown" id="sale-timer-end" data-end="{{ $saletimer->sale_timer->toISOString() }}">
                                <div><span class="number days"></span><span>Ngày</span></div>
                                <div><span class="number hours"></span><span>Giờ</span></div>
                                <div><span class="number minutes"></span><span>Phút</span></div>
                                <div><span class="number seconds"></span><span>Giây</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-carousel" style="margin-top: -30px;">
                    <div class="wow fadeIn animated" wire:ignore>
                        <div class="carausel-6-columns-cover position-relative">

                            <div class="carausel-6-columns carausel-arrow-center full-display" id="carausel-6-columns-1">
                                @foreach($saletimerproducts as $saletimerproduct)
                                <div class="product-cart-wrap small hover-up" style="width: 150px; margin: 5px; padding: 10px; font-size: 12px;">
                                    <div class="product-img-action-wrap" style="height: 150px; overflow: hidden;">
                                        <div class="product-img product-img-zoom">
                                            <a href="{{ route('details', ['slug' => $saletimerproduct->slug]) }}">
                                                <img class="default-img" src="{{ asset('admin/product/' . $saletimerproduct->image) }}" alt="" style="width: 100%; height: auto;">
                                            </a>
                                        </div>

                                    </div>
                                    <div class="product-content-wrap" style="text-align: center;">
                                        <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="{{ route('details', ['slug' => $saletimerproduct->slug]) }}">{{ $saletimerproduct->name }}</a>
                                        </h2>
                                        <div class="product-badges product-badges-position product-badges-mrg">
                                            @if($saletimerproduct->is_hot)
                                            <span class="hot">Hot</span>
                                            @endif
                                            @if($saletimerproduct->sale_price && $saletimerproduct->sale_price > 0 && $saletimerproduct->sale_price < $saletimerproduct->reguler_price && $saletimerproduct->reguler_price > 0)
                                                <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                                    - {{ round(($saletimerproduct->reguler_price - $saletimerproduct->sale_price) / $saletimerproduct->reguler_price * 100) }}%
                                                </span>
                                                @endif
                                        </div>
                                        <div class="product-sold" style="margin: 5px 0; font-size: 12px; color:rgb(234, 87, 50);">
                                            <span>Đã bán: {{ number_format($saletimerproduct->sold_quantity ?? 0, 0, ',', '.') }} sản phẩm</span>
                                        </div>
                                        <div class="product-price" style="font-size: 12px;">
                                            @if($saletimerproduct->sale_price && $saletimerproduct->sale_price > 0 && $saletimerproduct->sale_price < $saletimerproduct->reguler_price)
                                                <span style="color: #e74c3c; font-weight: bold;">{{ number_format($saletimerproduct->sale_price, 3, ',', '.') }}đ</span>
                                                <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 12px; margin-left: 5px;">{{ number_format($saletimerproduct->reguler_price, 3, ',', '.') }}đ</span>
                                                @else
                                                <span style="color: #333; font-weight: bold;">{{ number_format($saletimerproduct->reguler_price, 3, ',', '.') }}đ</span>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="container" wire:ignore>
            <div style="border: 1px solid #ccc; padding: 20px; border-radius: 10px; background-color: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3 class="section-title mb-20" style="color: #C12530; padding: 10px; border-radius: 5px;">
                    <i class="fas fa-th-large" style="margin-right: 8px; color: #000;"></i>
                    <span style="font-size: 22px">DANH MỤC </span>NỔI BẬT
                </h3>
                <div class="courses-grid" id="clients">
                    @foreach($categories->take(4) as $category)
                    <div class="item">
                        <a target="_blank" href="{{route('product.category', ['slug'=>$category->slug])}}">
                            <img src="{{asset('admin/category/'.$category->image)}}" alt="{{$category->name}}">
                            <div class="course-name"><b>{{$category->name}}</b></div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- TỦ SÁCH GỢI Ý -->
        <section class="popular-categories section-padding" wire:ignore style="margin-top: -30px;">
            <div class="container wow fadeIn animated">
                <h3 class="suggested-title"> <i class="fas fa-lightbulb"></i> <span>GỢI Ý CHO BẠN</span> <i class="fas fa-magic"></i></h3>
                <style>
                    .suggested-title {
                        background: linear-gradient(135deg, #ff4e50 0%, #ff6a00 40%, #ffb347 100%);
                        /* xanh chuối - vàng - xanh ngọc */
                        color: white;
                        padding: 14px 22px;
                        border-radius: 12px;
                        text-align: center;
                        font-size: 22px;
                        font-weight: 700;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 12px;
                        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                        letter-spacing: 1px;
                        text-transform: uppercase;
                        animation: pulseTitle 4s infinite;
                    }

                    .suggested-title i {
                        font-size: 24px;
                        animation: pop 2s infinite ease-in-out;
                    }

                    @keyframes pop {

                        0%,
                        100% {
                            transform: scale(1);
                        }

                        50% {
                            transform: scale(1.2);
                        }
                    }
                </style>
                <div class="carausel-6-columns-cover position-relative">
                    <div class="carausel-6-columns carausel-arrow-center full-display" id="carausel-6-columns-recommendations">
                        @forelse($recommendations as $recommendation)
                        <div class="product-cart-wrap small hover-up" style="width: 150px; margin: 5px; padding: 10px; font-size: 12px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #fafafa; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <div class="product-img-action-wrap" style="height: 150px; overflow: hidden;">
                                <div class="product-img product-img-zoom">
                                    <a href="{{ route('details', ['slug' => $recommendation->slug]) }}">
                                        <img class="default-img" src="{{ asset('admin/product/' . $recommendation->image) }}" alt="{{ $recommendation->name }}" style="width: 100%; height: auto;">
                                    </a>
                                </div>
                            </div>
                            <div class="product-content-wrap" style="text-align: center;">
                                <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="{{ route('details', ['slug' => $recommendation->slug]) }}">{{ $recommendation->name }}</a>
                                </h2>
                                <div class="product-badges product-badges-position product-badges-mrg">
                                    @if($recommendation->is_hot)
                                    <span class="hot">HOT</span>
                                    @endif
                                    @if($recommendation->sale_price && $recommendation->sale_price > 0 && $recommendation->sale_price < $recommendation->reguler_price && $recommendation->reguler_price > 0)
                                        <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                            - {{ round(($recommendation->reguler_price - $recommendation->sale_price) / $recommendation->reguler_price * 100) }}%
                                        </span>
                                        @endif
                                </div>
                                <div class="product-sold" style="margin: 5px 0; font-size: 12px; color: rgb(234, 87, 50);">
                                    <span>Đã giao: {{ number_format($recommendation->sold_quantity ?? 0, 0, ',', '.') }} sản phẩm</span>
                                </div>
                                <div class="product-price" style="font-size: 12px;">
                                    @if($recommendation->sale_price && $recommendation->sale_price > 0 && $recommendation->sale_price < $recommendation->reguler_price)
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <span style="color: #e74c3c; font-weight: bold;">{{ number_format($recommendation->sale_price, 3, ',', '.') }}đ</span>
                                            <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 12px;">{{ number_format($recommendation->reguler_price, 3, ',', '.') }}đ</span>
                                        </div>
                                        @else
                                        <span style="color: #333; font-weight: bold;">{{ number_format($recommendation->reguler_price, 0, ',', '.') }}đ</span>
                                        @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="product-cart-wrap small" style="width: 100%; text-align: center; padding: 20px;">
                            <p>Không có gợi ý nào hiện tại.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="popular-categories section-padding" wire:ignore style="margin-top: -80px;">
            <div class="container wow fadeIn animated">
                <h3 class="suggested-title-popular"> <i class="fas fa-fire-flame-curved"></i> <span>TỦ SÁCH BÁN CHẠY</span> <i class="fas fa-star"></i> </h3>
                <style>
                    .suggested-title-popular {
                        background: linear-gradient(135deg, #ff4e50 0%, #ff6a00 40%, #ffb347 100%);
                        color: white;
                        padding: 14px 22px;
                        border-radius: 10px;
                        text-align: center;
                        font-size: 22px;
                        font-weight: 600;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 10px;
                        box-shadow: 0 6px 15px rgba(255, 94, 0, 0.3);
                        letter-spacing: 0.5px;
                        text-transform: uppercase;
                    }

                    .suggested-title i {
                        font-size: 24px;
                        animation: pop 2s infinite ease-in-out;
                    }

                    @keyframes pop {

                        0%,
                        100% {
                            transform: scale(1);
                        }

                        50% {
                            transform: scale(1.2);
                        }
                    }
                </style>
                <div class="carausel-6-columns-cover position-relative">
                    <div class="carausel-6-columns carausel-arrow-center full-display" id="carausel-6-columns-3">
                        @foreach($bestproducts as $bestproduct)
                        <div class="product-cart-wrap small hover-up" style="width: 150px; margin: 5px; padding: 10px; font-size: 12px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #fafafa; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <div class="product-img-action-wrap" style="height: 150px; overflow: hidden;">
                                <div class="product-img product-img-zoom">
                                    <a href="{{route('details', ['slug'=>$bestproduct->slug])}}">
                                        <img class="default-img" src="{{ asset('admin/product/' . $bestproduct->image) }}" alt="" style="width: 100%; height: auto;">
                                    </a>
                                </div>

                            </div>
                            <div class="product-content-wrap" style="text-align: center;">
                                <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="{{route('details', ['slug'=>$bestproduct->slug])}}">{{$bestproduct->name}}</a>
                                </h2>
                                <div class="product-badges product-badges-position product-badges-mrg">
                                    @if($bestproduct->is_hot)
                                    <span class="hot">Hot</span>
                                    @endif
                                    @if($bestproduct->sale_price && $bestproduct->sale_price > 0 && $bestproduct->sale_price < $bestproduct->reguler_price && $bestproduct->reguler_price > 0)
                                        <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                            - {{ round(($bestproduct->reguler_price - $bestproduct->sale_price) / $bestproduct->reguler_price * 100) }}%
                                        </span>
                                        @endif
                                </div>
                                <div class="product-sold" style="margin: 5px 0; font-size: 12px; color:rgb(234, 87, 50);">
                                    <span>Đã bán: {{ number_format($bestproduct->sold_quantity ?? 0, 0, ',', '.') }} sản phẩm</span>
                                </div>
                                <div class="product-price" style="font-size: 12px;">
                                    @if($bestproduct->sale_price && $bestproduct->sale_price > 0 && $bestproduct->sale_price < $bestproduct->reguler_price)
                                        <div style="display: flex; align-items: center; gap: 5px; flex-wrap: nowrap;">
                                            <span style="color: #e74c3c; font-weight: bold;">{{ number_format($bestproduct->sale_price, 3, ',', '.') }}đ</span>
                                        </div>
                                        <div style="margin-top: 5px;">
                                            <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 12px;">{{ number_format($bestproduct->reguler_price, 3, ',', '.') }}đ</span>
                                        </div>
                                        @else
                                        <span style="color: #333; font-weight: bold;">{{ number_format($bestproduct->reguler_price, 3, ',', '.') }}đ</span>
                                        @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <div>
            <!-- TỦ SÁCH MỚI NHẤT -->
            <section class="popular-categories section-padding" wire:ignore style="margin-top: -30px;">
                <div class="container wow fadeIn animated">
                    <h3 class="latest-book-title"> <i class="fas fa-book-medical"></i> <span>TỦ SÁCH MỚI NHẤT</span> <i class="fas fa-clock"></i> </h3>
                    <style>
                        .latest-book-title {
                            background: linear-gradient(135deg, #ff4e50 0%, #ff6a00 40%, #ffb347 100%);
                            color: white;
                            padding: 14px 22px;
                            border-radius: 10px;
                            text-align: center;
                            font-size: 22px;
                            font-weight: 600px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            gap: 10px;
                            box-shadow: 0 4px 10px rgba(255, 130, 80, 0.3);
                            letter-spacing: 0.5px;
                            text-transform: uppercase;
                            margin-top: -50px;
                        }

                        .latest-book-title i {
                            font-size: 22px;
                            color: white;
                            animation: none;
                            /* không hiệu ứng nhảy */
                        }
                    </style>
                    <div class="carausel-6-columns-cover position-relative">
                        <div class="carausel-6-columns carausel-arrow-center full-display" id="carausel-6-columns-2">
                            @foreach($nproducts as $nproduct)
                            <div class="product-cart-wrap small hover-up" style="width: 150px; margin: 5px; padding: 10px; font-size: 12px;">
                                <div class="product-img-action-wrap" style="height: 150px; overflow: hidden;">
                                    <div class="product-img product-img-zoom">
                                        <a href="{{route('details', ['slug'=>$nproduct->slug])}}">
                                            <img class="default-img" src="{{ asset('admin/product/' . $nproduct->image) }}" alt="" style="width: 100%; height: auto;">
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap" style="text-align: center;">
                                    <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                        <a href="{{route('details', ['slug'=>$nproduct->slug])}}">{{$nproduct->name}}</a>
                                    </h2>
                                    <div class="product-badges product-badges-position product-badges-mrg">
                                        @if($nproduct->is_hot)
                                        <span class="hot">Hot</span>
                                        @endif
                                        @if($nproduct->sale_price && $nproduct->sale_price > 0 && $nproduct->sale_price < $nproduct->reguler_price && $nproduct->reguler_price > 0)
                                            <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                                - {{ round(($nproduct->reguler_price - $nproduct->sale_price) / $nproduct->reguler_price * 100) }}%
                                            </span>
                                            @endif
                                    </div>
                                    <div class="product-sold" style="margin: 5px 0; font-size: 12px; color:rgb(234, 87, 50);">
                                        <span>Đã bán: {{ number_format($nproduct->sold_quantity ?? 0, 0, ',', '.') }} sản phẩm</span>
                                    </div>
                                    <div class="product-price" style="font-size: 12px;">
                                        @if($nproduct->sale_price && $nproduct->sale_price > 0 && $nproduct->sale_price < $nproduct->reguler_price)
                                            <div style="display: flex; align-items: center; gap: 5px; flex-wrap: nowrap;">
                                                <span style="color: #e74c3c; font-weight: bold;">{{ number_format($nproduct->sale_price, 3, ',', '.') }}đ</span>
                                            </div>
                                            <div style="margin-top: 5px;">
                                                <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 12px;">{{ number_format($nproduct->reguler_price, 3, ',', '.') }}đ</span>
                                            </div>
                                            @else
                                            <span style="color: #333; font-weight: bold;">{{ number_format($nproduct->reguler_price, 3, ',', '.') }}đ</span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <section class="section-padding" wire:ignore style="margin-top: -10px;">
            <div class="container" style="background-color: white; padding: 20px; border-radius: 10px; margin-top: -50px;">
                <h3 class="section-title mb-20 wow fadeIn animated" style="color:#C12530;">
                    <i class="fas fa-star" style="margin-right: 8px; color:rgb(2, 2, 2);"></i>
                    <span style="font-size: 22px">THƯƠNG HIỆU</span> NỘI BẬT
                </h3>

                <style>
                    .img-grey-hover {
                        filter: none !important;
                        opacity: 1 !important;
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                    }

                    .img-grey-hover:hover {
                        transform: scale(1.05);
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
                    }
                </style>

                <div class="carausel-6-columns-cover position-relative wow fadeIn animated">
                    <div class="carausel-6-columns text-center" id="carausel-6-columns-4">
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-1.png" alt="">
                        </div>
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-2.png" alt="">
                        </div>
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-3.png" alt="">
                        </div>
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-4.png" alt="">
                        </div>
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-5.png" alt="">
                        </div>
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-6.png" alt="">
                        </div>
                        <div class="brand-logo">
                            <img class="img-grey-hover" src="assets/imgs/banner/brand-3.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Countdown Timer
                const countdownElement = document.getElementById('sale-timer-end');
                if (countdownElement) {
                    const endTime = new Date(countdownElement.dataset.end).getTime();
                    const updateCountdown = () => {
                        const now = new Date().getTime();
                        const timeLeft = endTime - now;

                        if (timeLeft <= 0) {
                            clearInterval(countdownInterval);
                            countdownElement.innerHTML = 'Đã kết thúc';
                            return;
                        }

                        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                        const secondsElement = countdownElement.querySelector('.seconds');
                        const minutesElement = countdownElement.querySelector('.minutes');
                        const hoursElement = countdownElement.querySelector('.hours');
                        const daysElement = countdownElement.querySelector('.days');

                        if (secondsElement && minutesElement && hoursElement && daysElement) {
                            secondsElement.textContent = seconds < 10 ? '0' + seconds : seconds;
                            minutesElement.textContent = minutes < 10 ? '0' + minutes : minutes;
                            hoursElement.textContent = hours < 10 ? '0' + hours : hours;
                            daysElement.textContent = days < 10 ? '0' + days : days;
                        } else {
                            console.error('Không tìm thấy các phần tử countdown trong DOM');
                            clearInterval(countdownInterval);
                        }
                    };

                    updateCountdown();
                    const countdownInterval = setInterval(updateCountdown, 1000);
                } else {
                    console.error('Không tìm thấy phần tử countdown trong DOM');
                }

                // Carousel Full Display Removal
                const carousels = [
                    document.getElementById('carausel-6-columns-1'), // FLASH SALE
                    document.getElementById('carausel-6-columns-2'), // TỦ SÁCH MỚI NHẤT
                    document.getElementById('carausel-6-columns-3'), // TỦ SÁCH BÁN CHẠY
                    document.getElementById('carausel-6-columns-recommendations') // CÓ THỂ BẠN SẼ THÍCH
                ];


                setTimeout(() => {
                    carousels.forEach(carousel => {
                        if (carousel) {
                            carousel.classList.remove('full-display');
                        }
                    });
                }, 2000);
            });
        </script>
</div>