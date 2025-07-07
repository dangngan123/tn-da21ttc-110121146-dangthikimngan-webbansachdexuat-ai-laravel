<div class="header-action-icon-2">
   
    @if(Cart::instance('wishlist')->count() > 0)
    <span class="pro-count blue">{{Cart::instance('wishlist')->count()}}</span>
    @endif
    </a>

</div>