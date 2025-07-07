<div>
    <div class="header-action-icon-2">
        <a class="mini-cart-icon" href="{{route('cart')}}">
            <img alt="" src="{{asset('/')}}assets/imgs/theme/icons/icon-cart.svg">
            @if(Cart::instance('cart')->count() > 0)
            <span class="pro-count blue">{{Cart::instance('cart')->count()}}</span>
            @endif
        </a>
        <div class="cart-dropdown-wrap cart-dropdown-hm2">
            <ul>
                @foreach(Cart::instance('cart')->content() as $item)
                <li>
                    <div class="shopping-cart-img">
                        <a href="{{route('details',['slug'=>$item->model->slug])}}"><img alt="Surfside Media" src="{{asset('admin/product/'.$item->model->image)}}"></a>
                    </div>
                    <div class="shopping-cart-title">
                        <h4>
                            <a href="{{ route('details', ['slug' => $item->model->slug]) }}">
                                {{ ucwords(mb_substr($item->model->name, 0, 20, 'UTF-8')) }}
                            </a>
                        </h4>

                        <h4><span>{{$item->qty}} x </span>{{$item->model->sale_price}}đ</h4>
                    </div>
                    <div class="shopping-cart-delete">
                        <a href="#" wire:click.prevent="remove('{{$item->rowId}}')"><i class="fi-rs-cross-small"></i></a>
                    </div>

                </li>
                @endforeach
            </ul>
            <div class="shopping-cart-footer">
                <div class="shopping-cart-total">
                    <h4>Tổng Cộng <span>{{Cart::instance('cart')->subtotal()}}đ</span></h4>
                </div>
                <div class="shopping-cart-button">
                    <a href="{{route('cart')}}" class="btn btn">Xem Giỏ Hàng</a>

                </div>
            </div>
        </div>
    </div>
</div>