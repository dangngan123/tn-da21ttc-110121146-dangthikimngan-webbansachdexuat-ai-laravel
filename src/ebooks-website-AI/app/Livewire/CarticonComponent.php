<?php

namespace App\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class CarticonComponent extends Component
{

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function remove($id)
    {
        Cart::instance('cart')->remove($id);
        flash('Mặt hàng trong giỏ hàng đã bị xóa.');
        $this->dispatch('refreshComponent')->to('cart-component');
        $this->dispatch('refreshComponent')->to('checkout-component');
    }
    public function render()
    {

        if (Auth::check()) {
            Cart::instance('cart')->store(Auth::user()->email);
            Cart::instance('wishlist')->store(Auth::user()->email);
        }
        return view('livewire.carticon-component');
    }
}
