<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <title>Panda - Nhà sách trực tuyến</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="">
    <meta property="og:type" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/imgs/theme/favicon.png') }}" sizes="16x16">
    <!-- Plugin CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/details.css') }}">

    <!-- Plugin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/icheck-material@1.0.1/icheck-material.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <!-- Thêm CSS jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireStyles
</head>

<body>
    <header class="header-area header-style-1 header-height-2">
        <div class="header-top header-top-ptb-1 d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-lg-4">
                    </div>
                    <div class="col-xl-6 col-lg-4">
                        <div class="text-center">
                            <div class="news-ticker">
                                <div class="ticker-content" style="font-weight: bold; font-size: 16px; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                                    <span>📘 Nhận ngay sách hay với ưu đãi giảm giá lên tới <strong style="color: #FFD700;">50%</strong>! <a href="{{ route('shop') }}" style="color: #FFD700; text-decoration: underline;">Xem Chi tiết</a></span>
                                    <span>🎁 Ưu đãi độc quyền – <strong style="color: #FFEB3B;">Tiết kiệm nhiều hơn</strong> với mã giảm giá!</span>
                                    <span>🔥 Kho sách hot – Giảm giá đến <strong style="color: #FFEB3B;">35%</strong> hôm nay! <a href="{{ route('shop') }}" style="color: #FFD700; text-decoration: underline;">Mua ngay</a></span>
                                    <span>🚚 <strong>Miễn phí vận chuyển</strong> cho đơn hàng từ 200.000đ!</span>
                                    <span>📦 Chương trình <strong>“Mua 2 tặng 1”</strong> áp dụng toàn bộ danh mục!</span>
                                    <span>🎉 Khách hàng thân thiết nhận ngay <strong>ưu đãi sinh nhật đặc biệt!</strong></span>
                                    <span>🆕 Sách mới cập bến – Ưu đãi lên tới <strong style="color: #FFD700;">40%</strong>! <a href="{{ route('shop') }}" style="color: #FFD700; text-decoration: underline;">Khám phá ngay</a></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
            <div class="container">
                <div class="header-wrap">
                    <div class="logo logo-width-1">
                        <a href="{{route('home')}}"><img src="{{asset('assets/imgs/logo/logo.png')}}" alt="logo"></a>
                    </div>
                    <div class="header-right">
                        @livewire('search-header-component')
                        <div class="header-action-right">
                            <div class="header-action-2">
                                @livewire('notification-icon-component')
                                @livewire('carticon-component')
                                @livewire('user-icon-component')
                                @php
                                $locale = session('locale', config('app.locale'));
                                @endphp

                                <div class="header-info">
                                    <ul class="dropdown-language-selector language-selector">
                                        <li>
                                            <a class="language-dropdown-active" href="javascript:void(0);">
                                                @if ($locale == 'vi')
                                                <img style="width:20px; height:20px; vertical-align:middle; margin-right:4px"
                                                    src="{{  asset('assets/imgs/theme/flag-vn.png')}}" alt="">
                                                VN
                                                @else
                                                <img style="width:20px; height:20px; vertical-align:middle; margin-right:4px"
                                                    src="{{  asset('assets/imgs/theme/flag-en.png')}}" alt="">

                                                EN
                                                @endif
                                                <i class="fi-rs-angle-small-down"></i>
                                            </a>

                                            <ul class="language-dropdown language-dropdown-visible" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; z-index:99;">
                                                @if ($locale != 'vi')
                                                <li style="margin-bottom: 8px;">
                                                    <a class="language-dropdown-active" href="{{ url('locale/vi') }}">
                                                        <img style="width:20px; height:20px; vertical-align:middle; margin-right:4px"
                                                            src=" {{ asset('assets/imgs/theme/flag-vn.png')}}" alt="">
                                                        VN
                                                    </a>
                                                </li>
                                                @endif

                                                @if ($locale != 'en')
                                                <li>
                                                    <a class="language-dropdown-active" href="{{ url('locale/en') }}">
                                                        <img style="width:20px; height:20px; vertical-align:middle; margin-right:4px"
                                                            src=" {{ asset('assets/imgs/theme/flag-en.png')}}" alt="">
                                                        EN
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <style>
                                    .header-info {
                                        padding: 5px;
                                    }


                                    .language-selector {
                                        list-style: none;
                                        padding: 0;
                                        margin: 0;
                                        position: relative;
                                    }

                                    .language-selector>li {
                                        display: inline-block;
                                        position: relative;
                                    }

                                    .language-dropdown-active {
                                        display: flex;
                                        align-items: center;
                                        text-decoration: none;
                                        color: #333;
                                        border: 1px solid #ddd;
                                        border-radius: 4px;
                                        padding: 5px 10px;
                                    }

                                    .language-dropdown {
                                        list-style: none;
                                        padding: 0;
                                        margin: 0;
                                        position: absolute;
                                        top: 100%;
                                        left: 0;
                                        background-color: #fff;
                                        border: 1px solid #ddd;
                                        border-radius: 4px;
                                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                                        min-width: 120px;
                                        z-index: 10;
                                        display: block;
                                        /* Hiển thị luôn, không cần hover */
                                        margin-top: 5px;
                                    }

                                    .language-dropdown li {
                                        padding: 0;
                                        margin: 0;
                                    }

                                    .language-dropdown a {
                                        display: flex;
                                        align-items: center;
                                        padding: 8px 12px;
                                        text-decoration: none;
                                        color: #333;
                                    }

                                    .language-dropdown a:hover {
                                        background-color: #f5f5f5;
                                    }
                                </style>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom header-bottom-bg-color sticky-bar">
            <div class="container">
                <div class="header-wrap header-space-between position-relative">
                    <!-- Logo -->
                    <div class="logo logo-width-1 d-block d-lg-none">
                        <a href="index.html"><img src="assets/imgs/logo/logo.png" alt="logo"></a>
                    </div>
                    <div class="header-nav d-none d-lg-flex">
                        <!-- <div class="main-categori-wrap d-none d-lg-block">
                            <a class="categori-button-active" href="#">
                                <span class="fi-rs-apps" style="font-size: 1.5em;"></span>@lang ('messages.categories')
                            </a>
                            <div class="categori-dropdown-wrap categori-dropdown-active-large">
                                <ul>
                                    <li><a href="product.category"><i class="surfsidemedia-font-desktop"></i>Computer & Office</a></li>
                                    <li><a href="product.category"><i class="surfsidemedia-font-cpu"></i>Consumer Electronics</a></li>
                                    <li><a href="product.category"><i class="surfsidemedia-font-diamond"></i>Jewelry & Accessories</a></li>
                                    <li><a href="product.category"><i class="surfsidemedia-font-home"></i>Home & Garden</a></li>
                                    <li><a href="product.category"><i class="surfsidemedia-font-high-heels"></i>Shoes</a></li>
                                    <li><a href="product.category"><i class="surfsidemedia-font-teddy-bear"></i>Mother & Kids</a></li>
                                    <li><a href="product.category"><i class="surfsidemedia-font-kite"></i>Outdoor fun</a></li>
                                </ul>
                            </div>

                        </div> -->
                        <!-- Main menu -->
                        <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block">
                            <nav>
                                <ul>
                                    <li>
                                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                            @lang('messages.home')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">
                                            @lang('messages.about')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('shop') }}" class="{{ request()->routeIs('shop') ? 'active' : '' }}">
                                            @lang('messages.shop')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                                            @lang('messages.contact')
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <!-- Phone -->
                    <div class="hotline d-none d-lg-block">
                        <p><i class="fi-rs-smartphone"></i><span></span> (+84) 795405536 </p>
                    </div>







                    <!-- Mobile -->
                    <!-- <p class="mobile-promotion">Happy <span class="text-brand">Mother's Day</span>. Big Sale Up to 40%</p> -->
                    <div class="header-action-right d-block d-lg-none">
                        <div class="header-action-2">
                            <div class="header-action-icon-2">
                                @livewire('carticon-component')
                            </div>
                            <div class="header-action-icon-2 d-block d-lg-none">
                                <div class="burger-icon burger-icon-white">
                                    <span class="burger-icon-top"></span>
                                    <span class="burger-icon-mid"></span>
                                    <span class="burger-icon-bottom"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="mobile-header-active mobile-header-wrapper-style">
        <div class="mobile-header-wrapper-inner">
            <div class="mobile-header-top">

                <!-- Logo -->
                <div class="mobile-header-logo">
                    <a href="index.html"><img src="{{ asset('assets/imgs/logo/logo.png') }}" alt="logo"></a>
                </div>


                <!-- <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                    <button class="close-style search-close">
                        <i class="icon-top"></i>
                        <i class="icon-bottom"></i>
                    </button>
                </div> -->


            </div>
            <div class="mobile-header-content-area">

                <div class="single-mobile-header-info">
                    <a href="{{ route('login') }}" class="btn btn-danger mb-2 text-white w-100">Đăng Nhập</a>
                </div>
                <div class="single-mobile-header-info">
                    <a href="{{ route('register') }}" class="btn btn-outline-custom w-100">Đăng Ký</a>
                </div>



                <div class="mobile-menu-wrap mobile-header-border">
                    <div class="main-categori-wrap mobile-header-border">
                        <a class="categori-button-active-2" href="#">
                            <span class="fi-rs-apps" style="font-size: 10PX;"></span>@lang ('messages.categories')
                        </a>

                        <div class="categori-dropdown-wrap categori-dropdown-active-small">
                            <ul>
                                <li><a href="shop.html"><i class="surfsidemedia-font-dress"></i>Women's Clothing</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-tshirt"></i>Men's Clothing</a></li>
                                <li> <a href="shop.html"><i class="surfsidemedia-font-smartphone"></i> Cellphones</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-desktop"></i>Computer & Office</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-cpu"></i>Consumer Electronics</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-home"></i>Home & Garden</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-high-heels"></i>Shoes</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-teddy-bear"></i>Mother & Kids</a></li>
                                <li><a href="shop.html"><i class="surfsidemedia-font-kite"></i>Outdoor fun</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- mobile menu start -->
                    <nav>
                        <ul class="mobile-menu">
                            <li>
                                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                    @lang('messages.home')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">
                                    @lang('messages.about')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('shop') }}" class="{{ request()->routeIs('shop') ? 'active' : '' }}">
                                    @lang('messages.shop')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                                    @lang('messages.contact')
                                </a>
                            </li>
                            <li class="menu-item-has-children"><span class="menu-expand"></span><a href="#">@lang('messages.language')</a>
                                <ul class="dropdown">
                                    @if ($locale != 'vi')
                                    <li>
                                        <a class="language-dropdown-active" href="{{ url('locale/vi') }}">
                                            <span>Tiếng việt</span> </a>
                                    </li>
                                    @endif
                                    @if ($locale != 'en')
                                    <li>
                                        <a class="language-dropdown-active" href="{{ url('locale/en') }}">
                                            <span>English</span></a>
                                    </li>
                                    @endif
                                </ul>
                            </li>

                        </ul>
                        </li>
                        </ul>
                    </nav>
                    <!-- mobile menu end -->
                </div>

                <div class="mobile-header-info-wrap mobile-header-border">
                    <div class="single-mobile-header-info mt-30">
                        <a href="contact.html"> Our location </a>
                    </div>
                    <div class="single-mobile-header-info">
                        <a href="login.html">Đăng Ký </a>
                    </div>
                    <div class="single-mobile-header-info">
                        <a href="register.html">Đăng Ký</a>
                    </div>
                    <div class="single-mobile-header-info">
                        <a href="#">(+84) 1900-636-466 </a>
                    </div>
                </div>
               
            </div>
        </div>
    </div>





    {{ $slot }}
    <footer class="main">
        <!-- <section class="newsletter p-30 text-white wow fadeIn animated">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7 mb-md-3 mb-lg-0">
                        <div class="row align-items-center">
                            <div class="col flex-horizontal-center">
                                <img class="icon-email" src="{{asset('assets/imgs/theme/icons/icon-email.svg')}}" alt="">
                                <h4 class="font-size-20 mb-0 ml-3">Đăng kí nhận tin</h4>
                            </div>
                            <div class="col my-4 my-md-0 des">
                                <h5 class="font-size-15 ml-4 mb-0">... và nhận phiếu giảm giá <strong>50% cho lần mua sắm đầu tiên.</strong></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                       
                        <form class="form-subcriber d-flex wow fadeIn animated">
                            <input type="email" class="form-control bg-white font-small" placeholder="Enter your email">
                            <button class="btn bg-dark text-white" type="submit">Subscribe</button>
                        </form>
                       
                    </div>
                </div>
            </div>
        </section> -->
        <section class="section-padding footer-mid" style="background-color:rgb(225, 225, 225);">
            <div class="container pt-15 pb-20" >
                <div class="row" >
                    <div class="col-lg-4 col-md-6">
                        <div class="widget-about font-md mb-md-5 mb-lg-0">
                            <div class="logo logo-width-1 wow fadeIn animated">
                                <a href="index.html"><img src="{{asset('assets/imgs/logo/logo.png')}}" alt="logo"></a>
                            </div>
                            <h3 class="mt-20 mb-10 fw-600 text-grey-4 wow fadeIn animated">Liên Hệ</h3>
                            <p class="wow fadeIn animated">
                                <strong>Địa Chỉ: </strong> 126 Nguyễn Thiện Thành, Phường 5, Trà Vinh
                            </p>
                            <p class="wow fadeIn animated">
                                <strong>Phone: </strong>(+84) 795405536
                            </p>
                            <p class="wow fadeIn animated">
                                <strong>Email: </strong>iamkimngan@gmail.com
                            </p>
                            <h5 class="mb-10 mt-30 fw-600 text-grey-4 wow fadeIn animated">Follow Us</h5>
                            <div class="mobile-social-icon wow fadeIn animated mb-sm-5 mb-md-0">
                                <a href="#"><img src="{{asset('assets/imgs/theme/icons/icon-facebook.svg')}}" alt=""></a>
                                <a href="#"><img src="{{asset('assets/imgs/theme/icons/icon-twitter.svg')}}" alt=""></a>
                                <a href="#"><img src="{{asset('assets/imgs/theme/icons/icon-instagram.svg')}}" alt=""></a>
                                <a href="#"><img src="{{asset('assets/imgs/theme/icons/icon-pinterest.svg')}}" alt=""></a>
                                <a href="#"><img src="{{asset('assets/imgs/theme/icons/icon-youtube.svg')}}" alt=""></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <h5 class="widget-title wow fadeIn animated">DỊCH VỤ</h5>
                        <ul class="footer-list wow fadeIn animated mb-sm-5 mb-md-0">
                            <li><a href="{{ route('policy') }}">Điều khoản sử dụng</a></li>
                            <li><a href="{{ route('policy') }}">Chính sách bảo mật thông tin cá nhân</a></li>
                            <li><a href="{{ route('policy') }}">Chính sách bảo mật thanh toán</a></li>
                            <li><a href="{{ route('about') }}">Giới thiệu Panda</a></li>

                        </ul>
                    </div>
                    <div class="col-lg-2  col-md-3">
                        <h5 class="widget-title wow fadeIn animated">HỖ TRỢ</h5>
                        <ul class="footer-list wow fadeIn animated">
                            <li><a href="{{ route('policy') }}">Chính sách đổi trả - hoàn tiền</a></li>
                            <li><a href="{{ route('policy') }}">Chính sách bảo hành - bồi hoàn</a></li>
                            <li><a href="{{ route('policy') }}">Chính sách vận chuyển</a></li>
                            <li><a href="{{ route('policy') }}">Chính sách khách sỉ</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 mob-center">
                        <h5 class="widget-title wow fadeIn animated">TÀI KHOẢN CỦA TÔI</h5>
                        <ul class="footer-list wow fadeIn animated">
                            <li><a href="{{ route('login') }}">Đăng nhập/Đăng kí tài khoản</a></li>
                            <li><a href="{{ route('customer.dashboard') }}">Thay đổi địa chỉ khách hàng</a></li>
                            <li><a href="{{ route('customer.dashboard') }}">Chi tiết tài khoản</a></li>
                            <li><a href="{{ route('customer.orders') }}">Lịch sử mua hàng</a></li>
                        </ul>
                    </div>
                    <!-- <div class="col-lg-4 mob-center">
                        <h5 class="widget-title wow fadeIn animated">Install App</h5>
                        <div class="row">
                            <div class="col-md-8 col-lg-12">
                                <p class="wow fadeIn animated">From App Store or Google Play</p>
                                <div class="download-app wow fadeIn animated mob-app">
                                    <a href="#" class="hover-up mb-sm-4 mb-lg-0"><img class="active" src="{{asset('assets/imgs/theme/app-store.jpg')}}" alt=""></a>
                                    <a href="#" class="hover-up"><img src="{{asset('assets/imgs/theme/google-play.jpg')}}" alt=""></a>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-12 mt-md-3 mt-lg-0">
                                <p class="mb-20 wow fadeIn animated">Secured Payment Gateways</p>
                                <img class="wow fadeIn animated" src="{{asset('assets/imgs/theme/payment-method.png')}}" alt="">
                            </div>
                        </div>
                    </div> -->

                </div>
            </div>
        </section>
        <!-- <br>
        <div class="container pb-20 wow fadeIn animated mob-center">
            <div class="row" style="margin-top: -30px;">
                <div class="col-12 mb-20">
                    <div class="footer-bottom"></div>
                </div>
                <div class="col-lg-6">
                    <p class="text-lg-start text-center font-sm text-muted mb-0">
                        <a href="{{ route('home') }}">Panda.com</a> - Nhà sách trực tuyến hàng đầu Việt Nam
                    <p class="text-lg-end text-start font-sm text-muted mb-0">
                </div>

            </div>
        </div> -->
    </footer>
    @if (!in_array(request()->route()->getName(), ['checkout', 'admin.dashboard', 'cart', ]))
    <livewire:chatbot-component />
    @endif
    <!-- Vendor JS-->
    <script src="{{ asset('assets/js/vendor/modernizr-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/lightbox-plus-jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/slick.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.syotimer.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/wow.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/magnific-popup.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/waypoints.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/counterup.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/images-loaded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/isotope.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/scrollup.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.vticker-min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.theia.sticky.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery.elevatezoom.js') }}"></script>










    <!-- Template  JS -->
    <script src="{{asset('assets/js/main.js?v=3.3')}}"></script>
    <script src="{{ asset('assets/js/shop.js?v=3.3') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.3.0.min.js"></script>

    @livewireScripts
    @stack('scripts')
</body>




<script>
    // //hiển thị thêm phương thức vận chuyển
    // window.addEventListener('add-show-shipping-modal', event => {
    //     $('#addShipingModal').modal('show');
    // })
    // window.addEventListener('hide-shipping-modal', event => {
    //     $('#addShipingModal').modal('hide');
    // })

    //hiển thị cập nhật phương thức vận chuyển
    window.addEventListener('shipping-modal', event => {
        $('#ShipingModal').modal('show');
        $('#ShipingModal').modal('hide');
    })

    window.addEventListener('product-quick-view', event => {
        $('#ProductQuickViewModal').modal('show');
        $('#ProductQuickViewModal').modal('hide');

        //
    })
    // admin dashboard
    //Slider
    window.addEventListener('rafa-modal', event => {
        $('#rafaModal').modal('show');
        $('#rafaModal').modal('hide');
    })
    //danh mục
    window.addEventListener('category-modal', event => {
        $('#categoryModal').modal('show');
        $('#categoryModal').modal('hide');
    })
    //sản phẩm
    window.addEventListener('product-modal', event => {
        $('#productModal').modal('show');
        $('#productModal').modal('hide');
    })
    //chi tiet san phẩm
    window.addEventListener('product-detail-modal', event => {
        $('#productDetailModal').modal('show');
        $('#productDetailModal').modal('hide');
    })

    //mã giảm giá
    window.addEventListener('coupon-modal', event => {
        $('#couponModal').modal('show');
        $('#couponModal').modal('hide');
    })
</script>



<script>
    window.addEventListener('show-delete-confirmation', event => {
        Swal.fire({
            title: "Bạn có chắc chắn không?",
            text: "Bạn sẽ không thể hoàn tác hành động này!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, xóa ngay!",
            cancelButtonText: "Hủy bỏ"
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteConfirmed');
            }
        });
    });







    //Language dropdown
    // Hiển thị / ẩn menu khi bấm vào dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.querySelector('.language-dropdown-active');
        const dropdown = document.querySelector('.language-dropdown');

        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Tắt menu khi click bên ngoài
        document.addEventListener('click', function(e) {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    });
</script>

  <script>
        // Định nghĩa biến dailyOrdersOptions để tránh lỗi
        var dailyOrdersOptions = {
            series: [],
            chart: {
                type: 'bar',
                height: 300
            }
        };
    </script>







</body>

</html>