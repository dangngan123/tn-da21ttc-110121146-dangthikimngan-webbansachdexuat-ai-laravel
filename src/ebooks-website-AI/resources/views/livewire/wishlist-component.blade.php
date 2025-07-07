<div>
    <style>
        .product-grid-3 {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .product-grid-3>[class*="col-"] {
            padding-right: 15px;
            padding-left: 15px;
            display: flex;
            /* Sử dụng Flexbox để đồng bộ chiều cao */
        }

        .product-cart-wrap {
            flex: 1;
            /* Đảm bảo khung sản phẩm chiếm toàn bộ không gian khả dụng */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Phân bổ đều nội dung bên trong */
        }

        .product-content-wrap {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
            /* Đảm bảo phần nội dung chiếm toàn bộ không gian còn lại */
        }

        .product-content-wrap h2 {
            min-height: 40px;
            /* Đặt chiều cao tối thiểu cho tên sản phẩm (tương ứng 2 dòng) */
            font-size: 13px;
            margin: 5px 0;
            text-align: left;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-sold,
        .product-price {
            margin: 5px 0;
        }
    </style>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}" rel="nofollow">Home</a>
                    <span></span> Danh sách yêu thích
                </div>
            </div>
        </div>
        <section class="mt-50 mb-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @if(empty($wishlistData))
                        <div class="alert alert-info">
                            Danh sách yêu thích của bạn đang trống. Hãy thêm sản phẩm vào danh sách yêu thích để xem chúng tại đây!
                        </div>
                        @else
                        <div class="row product-grid-3">
                            @foreach($wishlistData as $data)
                            <div class="col-lg-3 col-md-3 col-6 col-sm-6">
                                <div class="product-cart-wrap mb-30">
                                    <div class="product-img-action-wrap">
                                        <div class="product-img product-img-zoom">
                                            <a href="{{route('details', ['slug'=>$data['model']->slug])}}">
                                                <img class="default-img" src="{{asset('admin/product/'.$data['model']->image)}}" alt="">
                                            </a>
                                        </div>
                                        <div class="product-badges product-badges-position product-badges-mrg">
                                            @if($data['model']->is_hot)
                                            <span class="hot">Hot</span>
                                            @endif
                                            @if($data['model']->sale_price && $data['model']->sale_price > 0 && $data['model']->sale_price < $data['model']->reguler_price && $data['model']->reguler_price > 0)
                                                <span class="discount" style="background: #e74c3c; color: #fff; font-size: 15px; padding: 2px 5px;">
                                                    - {{ round(($data['model']->reguler_price - $data['model']->sale_price) / $data['model']->reguler_price * 100) }}%
                                                </span>
                                                @endif
                                        </div>
                                    </div>
                                    <div class="product-content-wrap">
                                        <h2 style="font-size: 13px; margin: 5px 0; text-align: left; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="{{route('details', ['slug'=>$data['model']->slug])}}">{{$data['model']->name}}</a>
                                        </h2>
                                        <div class="product-sold" style="margin: 5px 0; font-size: 12px; color:rgb(234, 87, 50);">
                                            @if($data['sold_quantity'] > 0)
                                            <span>Đã bán: {{ number_format($data['sold_quantity'], 0, ',', '.') }} sản phẩm</span>
                                            @else
                                            <span>Đã bán: 0 sản phẩm</span>
                                            @endif
                                        </div>
                                        <div class="product-price">
                                            <span>{{ number_format($data['model']->sale_price, 3, ',', '.') }} đ</span>
                                            <span class="old-price">{{ number_format($data['model']->reguler_price, 3, ',', '.') }} đ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>