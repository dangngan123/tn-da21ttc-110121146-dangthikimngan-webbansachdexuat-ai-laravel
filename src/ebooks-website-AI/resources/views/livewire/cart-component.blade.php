<div>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}" rel="nofollow">Trang chủ</a>
                    <span></span> <a href="{{ route('shop') }}" rel="nofollow">Sản phẩm</a>
                    <span></span> Giỏ hàng
                </div>
            </div>
        </div>
        <section class="mt-10 mb-10">
            <div class="container">
                @if(Cart::instance('cart')->count() > 0)
                <div class="row">
                    <div class="col-7">
                        <div class="table-responsive">
                            <table class="table shopping-summery text-center clean">
                                <thead>
                                    <tr class="main-heading">
                                        <th scope="col">Ảnh</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">Giá</th>
                                        <th scope="col">Số Lượng</th>
                                        <th scope="col">Thành Tiền</th>
                                        <th scope="col">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(Cart::instance('cart')->content() as $item)
                                    <tr>
                                        <td class="image product-thumbnail"><img src="{{ asset('admin/product/'.$item->model->image) }}" alt="#"></td>
                                        <td class="product-des product-name">
                                            <h5 class="product-name" style="font-size: 12px; margin: 5px 0;"><a href="{{ route('details', ['slug' => $item->model->slug]) }}">{{ $item->model->name }}</a></h5>
                                        </td>
                                        <td class="price" data-title="Price"><span>{{ number_format($item->price ?? $item->model->sale_price, 3, ',', '.') }}đ</span></td>
                                        <td class="text-center" data-title="Stock">
                                            <div class="quantity-field">
                                                <button class="value-button decrease-button" title="Azalt" wire:click.prevent="decreaseQuantity('{{$item->rowId}}')">-</button>
                                                <div class="number">{{ $item->qty }}</div>
                                                <button class="value-button increase-button" title="Arrtır" wire:click.prevent="increaseQuantity('{{$item->rowId}}')">+</button>
                                            </div>
                                        </td>
                                        <td class="text-right" data-title="Cart">
                                            <span>{{ number_format($item->subtotal(), 3, ',', '.') }}đ</span>
                                        </td>
                                        <td class="action" data-title="Remove"><a href="#" class="text-muted" wire:click.prevent="destroy('{{$item->rowId}}')"><i class="fi-rs-trash"></i></a></td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="6" class="text-end">
                                            <a href="#" class="text-muted" wire:click.prevent="ClearCart()"> <i class="fi-rs-cross-small"></i> Xóa tất cả</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-5">
                        <div class="row mb-10">
                            <div class="col-lg-12 col-md-12">
                                <div class="border p-md-4 p-30 border-radius cart-totals">
                                    <div class="heading_s1 mb-3">
                                        <h4>Giỏ Hàng</h4>
                                    </div>
                                    <div class="table-responsive">
                                        @if(session()->has('coupon'))
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="cart_total_label" style="text-align: left;">Thành Tiền</td>
                                                    <td class="cart_total_amount" style="text-align: right;"><strong><span class="font-xl fw-900 text-brand">{{ number_format(Cart::instance('cart')->subtotal(), 3, ',', '.') }}đ</span></strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="cart_total_label">Mã ({{ session()->get('coupon')['coupon_code'] }}) Giảm Giá</td>
                                                    <td class="cart_total_amount" style="text-align: right;"><span class="font-lg fw-900 text-brand">- {{ number_format($discount, 3, ',', '.') }}đ</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="cart_total_label">Phí vận chuyển (Giao hàng tiêu chuẩn)</td>
                                                    <td class="cart_total_amount" style="text-align: right;">
                                                        <span class="font-lg fw-900 text-brand">
                                                            @if($shippingFee == 0)
                                                                Miễn phí
                                                            @else
                                                                {{ number_format($shippingFee, 3, ',', '.') }}đ
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="cart_total_label">Tổng Số Tiền</td>
                                                    <td class="cart_total_amount" style="text-align: right;"><span class="font-xl fw-900 text-brand">{{ number_format($totalAfterDiscount, 3, ',', '.') }}đ</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="mt-3 text-end">
                                            <button class="btn btn-sm" style="background-color: #28a745; color: #fff; border: none; margin: 10px 0;" wire:click.prevent="removeCoupon">Hủy mã</button>
                                        </div>
                                        @else
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="cart_total_label">Thành Tiền</td>
                                                    <td class="cart_total_amount"><span class="font-lg fw-900 text-brand">{{ number_format(Cart::instance('cart')->subtotal(), 3, ',', '.') }}đ</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="cart_total_label">Phí vận chuyển (Giao hàng tiêu chuẩn)</td>
                                                    <td class="cart_total_amount">
                                                        <div style="display: flex; justify-content: center; align-items: center;">
                                                            <i class="ti-gift mr-5"></i>
                                                            <p class="text-info" style="margin: 0; text-align: center;">
                                                                @if($shippingFee == 0)
                                                                    Miễn phí
                                                                @else
                                                                    {{ number_format($shippingFee, 3, ',', '.') }}đ
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="cart_total_label">Tổng Số Tiền</td>
                                                    <td class="cart_total_amount"><strong><span class="font-xl fw-900 text-brand">{{ number_format(Cart::instance('cart')->subtotal() + $shippingFee, 3, ',', '.') }}đ</span></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        @endif
                                    </div>

                                    @if(!session()->has('coupon'))
                                    <div class="coupon-section mt-4">
                                        <div class="heading_s1 mb-3">
                                            <h5 class="fw-bold text-primary"><i class="fas fa-tags me-2"></i> Mã Giảm Giá</h5>
                                        </div>
                                        <div class="available-coupons mb-4">
                                            @if($bestCoupon)
                                            <div class="best-coupon p-3 mb-4 rounded shadow-sm" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 1px solid #c3e6cb;">
                                                <div class="d-flex flex-row align-items-center justify-content-between" style="flex-wrap: nowrap;">
                                                    <div style="flex: 1; margin-right: 10px;">
                                                        <h6 class="mb-1 text-success fw-bold"><i class="fas fa-crown me-1"></i> Mã Tốt Nhất: {{ $bestCoupon['coupon_code'] }}</h6>
                                                        <p class="mb-2 text-dark" style="font-size: 12px;">
                                                            Giảm <span class="fw-bold text-danger">{{ number_format($bestCoupon['discount'], 3, ',', '.') }}đ</span>
                                                            ({{ $bestCoupon['coupon_type'] == 'fixed' ? number_format($bestCoupon['coupon_value'], 3, ',', '.') . 'đ' : $bestCoupon['coupon_value'] . '%' }})
                                                            cho đơn từ {{ number_format($bestCoupon['cart_value'], 3, ',', '.') }}đ
                                                        </p>
                                                    </div>
                                                    <button class="btn btn-sm square-btn custom-blue-btn" wire:click.prevent="applyCoupon('{{ $bestCoupon['coupon_code'] }}')">
                                                        Áp dụng
                                                    </button>
                                                </div>
                                            </div>
                                            @endif

                                            @if($applicableCoupons->count() > 0)
                                            <div class="other-coupons">
                                                <h6 class="mb-3 text-muted fw-bold">Các mã giảm giá khác:</h6>
                                                <ul class="list-unstyled">
                                                    @foreach($applicableCoupons as $index => $coupon)
                                                        @if(($bestCoupon && $coupon->coupon_code != $bestCoupon['coupon_code']) || !$bestCoupon)
                                                            @if($index < 2 || $showAllCoupons)
                                                            <li class="coupon-item d-flex flex-row align-items-center justify-content-between mb-4" style="flex-wrap: nowrap;">
                                                                <span class="text-dark" style="font-size: 12px; flex: 1; margin-right: 10px;">
                                                                    <i class="fas fa-tag text-primary me-1"></i> {{ $coupon->coupon_code }}
                                                                    ({{ $coupon->coupon_type == 'fixed' ? number_format($coupon->coupon_value, 3, ',', '.') . 'đ' : $coupon->coupon_value . '%' }})
                                                                    - Đơn từ {{ number_format($coupon->cart_value, 3, ',', '.') }}đ
                                                                </span>
                                                                <button class="btn btn-sm square-btn custom-blue-btn" wire:click.prevent="applyCoupon('{{ $coupon->coupon_code }}')">
                                                                    Áp dụng
                                                                </button>
                                                            </li>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </ul>
                                                @if($applicableCoupons->count() > ($bestCoupon ? 3 : 2))
                                                <div class="text-center mt-3">
                                                    <a href="#" wire:click.prevent="toggleShowAllCoupons" class="text-primary text-decoration-none fw-bold">
                                                        {{ $showAllCoupons ? 'Ẩn bớt' : 'Xem thêm' }} 
                                                        ({{ $applicableCoupons->count() - ($bestCoupon ? 1 : 0) - ($showAllCoupons ? 0 : 2) }})
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                            @else
                                            <p class="text-muted text-center py-3">Không có mã giảm giá khả dụng.</p>
                                            @endif
                                        </div>

                                        <div class="coupon-form mt-4">
                                            <form action="#" target="_blank">
                                                <div class="input-group">
                                                    <input type="text" class="form-control rounded-start-pill" placeholder="Nhập mã giảm giá" wire:model="couponCode">
                                                    <button class="btn btn-dark rounded-end-pill px-4" wire:click.prevent="applyCouponCode">
                                                        <i class="fas fa-ticket-alt me-1"></i> Áp mã
                                                    </button>
                                                </div>
                                            </form>
                                            @if (session()->has('error_message'))
                                            <div class="alert alert-danger alert-dismissible fade show mt-3 rounded" role="alert">
                                                {{ session('error_message') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                            @endif
                                            @if (session()->has('success_message'))
                                            <div class="alert alert-success alert-dismissible fade show mt-3 rounded" role="alert">
                                                {{ session('success_message') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    <a href="#" class="btn w-100" style="background-color: #C12530; color: #fff; border: none;" wire:click.prevent="checkout()"> <i class="fi-rs-box-alt mr-10"></i> THANH TOÁN</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <style>
                                .btn-container {
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    height: 100%;
                                    margin-top: 20px;
                                }
                                .btn:hover {
                                    background-color: #C12530;
                                }
                            </style>
                            <img src="{{ asset('assets/imgs/cart/ico_emptycart.svg') }}" alt="" width="150px">
                            <p class="text-center" style="font-size: 14px;">Chưa có sản phẩm trong giỏ hàng của bạn.</p>
                            <div class="btn-container">
                                <a href="{{ route('shop') }}" class="btn">Mua Sắm Ngay</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="divider mt-5 mb-15"></div>
                    <h3 class="section-title mb-20"><span>ĐỀ XUẤT </span> MỚI</h3>
                    <div class="col-lg-12">
                        <div class="row product-grid-3 g-1">
                            @foreach ($products as $product)
                            <div class="col-lg-2 col-md-2 col-6 col-sm-6">
                                <div class="product-cart-wrap mb-1">
                                    <div class="product-img-action-wrap">
                                        <div class="product-img product-img-zoom">
                                            <a href="{{ route('details', $product->slug) }}">
                                                <img class="default-img" src="{{ asset('admin/product/'.$product->image) }}" alt="">
                                            </a>
                                        </div>
                                        <div class="product-badges product-badges-position product-badges-mrg">
                                            @if($product->is_hot)
                                            <span class="hot">Hot</span>
                                            @endif
                                            @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->reguler_price && $product->reguler_price > 0)
                                                <span class="discount" style="background: #e74c3c; color: #fff; font-size: 10px; padding: 2px 5px;">
                                                    - {{ round(($product->reguler_price - $product->sale_price) / $product->reguler_price * 100) }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="product-content-wrap">
                                        <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="{{ route('details', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                                        </h2>
                                        <div class="product-price" style="font-size: 14px; display: flex; flex-wrap: wrap; align-items: center; gap: 5px;">
                                            @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->reguler_price)
                                                <span style="color: #e74c3c; font-weight: bold;">{{ number_format($product->sale_price, 3, ',', '.') }}đ</span>
                                                <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 12px; margin-left: 5px;">{{ number_format($product->reguler_price, 3, ',', '.') }}đ</span>
                                            @else
                                                <span style="color: #333; font-weight: bold;">{{ number_format($product->reguler_price, 3, ',', '.') }}đ</span>
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

    <style>
        .quantity-field {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 120px;
            height: 40px;
            margin: 0 auto;
        }
        .quantity-field .value-button {
            border: 1px solid #ddd;
            margin: 0px;
            width: 40px;
            height: 100%;
            padding: 0;
            background: #eee;
            outline: none;
            cursor: pointer;
        }
        .quantity-field .value-button:hover {
            background: rgb(230, 230, 230);
        }
        .quantity-field .value-button:active {
            background: rgb(210, 210, 210);
        }
        .quantity-field .decrease-button {
            margin-right: -4px;
            border-radius: 8px 0 0 8px;
        }
        .quantity-field .increase-button {
            margin-left: -4px;
            border-radius: 0 8px 8px 0;
        }
        .quantity-field .number {
            display: inline-block;
            text-align: center;
            border: none;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            margin: 0px;
            width: 40px;
            height: 100%;
            line-height: 40px;
            font-size: 11pt;
            box-sizing: border-box;
            background: white;
            font-family: calibri;
        }
        .quantity-field .number::selection {
            background: none;
        }
        .coupon-section {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .best-coupon {
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
        }
        .best-coupon:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .coupon-item {
            transition: background 0.2s, transform 0.2s;
            margin-bottom: 20px;
        }
        .coupon-item:hover {
            background: #e9ecef !important;
            transform: translateY(-2px);
        }
        .btn {
            transition: background 0.2s, transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .input-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }
        .alert {
            font-size: 12px;
            padding: 10px 15px;
        }
        .rounded-start-pill {
            border-top-left-radius: 50rem !important;
            border-bottom-left-radius: 50rem !important;
        }
        .rounded-end-pill {
            border-top-right-radius: 50rem !important;
            border-bottom-right-radius: 50rem !important;
        }
        .square-btn {
            padding: 4px 8px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 4px !important;
        }
        .custom-blue-btn {
            background-color: #2F80ED;
            color: #fff;
            border: none;
        }
        .custom-blue-btn:hover {
            background-color: #1b5db1;
            color: #fff;
        }
    </style>
</div>