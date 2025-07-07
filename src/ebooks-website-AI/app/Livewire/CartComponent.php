<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;
use Carbon\Carbon;

class CartComponent extends Component
{
    public $couponCode = '';
    public $showAllCoupons = false;
    public $coupons = [];
    public $applicableCoupons = [];
    public $bestCoupon = null;
    public $discount;
    public $subtotalAfterDiscount;
    public $totalAfterDiscount;
    public $shippingFee;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->updateShippingFee();
        $this->loadCoupons();
        $this->findBestCoupon();
    }

    public function updateShippingFee()
    {
        $subtotal = floatval(Cart::instance('cart')->subtotal());
        $this->shippingFee = $subtotal >= 200 ? 0 : 20;
    }

    public function loadCoupons()
    {
        try {
            $subtotal = Cart::instance('cart')->subtotal();
            $query = Coupon::where('start_date', '<=', Carbon::today())
                ->where('end_date', '>=', Carbon::today())
                ->where('is_active', true)
                ->where('cart_value', '<=', $subtotal)
                ->where(function ($query) {
                    $query->whereNull('max_uses')
                          ->orWhereColumn('used', '<', 'max_uses');
                });

            if (Auth::check()) {
                $query->where(function ($query) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', Auth::id());
                });
            } else {
                $query->whereNull('user_id');
            }

            $this->coupons = $query->get();
            $this->applicableCoupons = $this->coupons;
        } catch (\Exception $e) {
            $this->coupons = [];
            $this->applicableCoupons = [];
            flash()->error('Không thể tải danh sách mã giảm giá.');
        }
    }

    public function findBestCoupon()
    {
        $this->bestCoupon = null;
        $maxDiscount = 0;
        $subtotal = Cart::instance('cart')->subtotal();

        foreach ($this->coupons as $coupon) {
            if ($coupon->cart_value <= $subtotal && 
                ($coupon->max_uses === null || $coupon->used < $coupon->max_uses) &&
                ($coupon->user_id === null || (Auth::check() && $coupon->user_id == Auth::id()))) {
                
                $discount = $coupon->coupon_type == 'fixed' 
                    ? $coupon->coupon_value 
                    : ($subtotal * $coupon->coupon_value) / 100;

                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                    $this->bestCoupon = [
                        'coupon_code' => $coupon->coupon_code,
                        'coupon_type' => $coupon->coupon_type,
                        'coupon_value' => $coupon->coupon_value,
                        'discount' => $discount,
                        'cart_value' => $coupon->cart_value,
                    ];
                }
            }
        }
    }

    public function increaseQuantity($id)
    {
        $product = Cart::instance('cart')->get($id);
        $myproduct = Product::where('id', $product->id)->first();

        if ($myproduct->quantity > $product->qty) {
            $qty = $product->qty + 1;
            Cart::instance('cart')->update($id, $qty);
            $this->updateShippingFee();
            $this->loadCoupons();
        } else {
            flash()->error('Số lượng không đủ trong kho.');
        }
        $this->dispatch('refreshComponent')->to('carticon-component');
    }

    public function decreaseQuantity($id)
    {
        try {
            $product = Cart::instance('cart')->get($id);
            $qty = $product->qty - 1;
            Cart::instance('cart')->update($id, $qty);
            $this->updateShippingFee();
            $this->loadCoupons();
            $this->dispatch('refreshComponent')->to('carticon-component');
        } catch (\Exception $e) {
        }
    }

    public function destroy($id)
    {
        Cart::instance('cart')->remove($id);
        $this->updateShippingFee();
        $this->loadCoupons();
        flash('Mặt hàng trong giỏ hàng đã bị xóa.');
        $this->dispatch('refreshComponent')->to('carticon-component');
    }

    public function ClearCart()
    {
        Cart::instance('cart')->destroy();
        $this->updateShippingFee();
        $this->loadCoupons();
        flash('Tất cả giỏ hàng đã bị xóa.');
        $this->dispatch('refreshComponent')->to('carticon-component');
    }

    public function checkout()
    {
        if (Auth::check()) {
            foreach (Cart::instance('cart')->content() as $item) {
                $product = Product::find($item->id);
                if ($product && $product->quantity < $item->qty) {
                    flash()->error("Sản phẩm '{$product->name}' không đủ số lượng trong kho hiện số lượng trong chỉ có '{$product->quantity}' sản phẩm.");
                    return redirect()->route('cart');
                }
            }
            return redirect()->route('checkout');
        } else {
            return redirect()->route('login');
        }
    }

    public function applyCoupon($code)
    {
        $this->couponCode = $code;
        $this->applyCouponCode();
    }

    public function applyCouponCode()
    {
        if (session()->has('coupon')) {
            session()->flash('error_message', 'Mã giảm giá đã được áp dụng cho đơn hàng này.');
            return;
        }

        $coupon = Coupon::where('coupon_code', $this->couponCode)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            session()->flash('error_message', 'Mã giảm giá không hợp lệ, đã hết hạn hoặc không đủ điều kiện áp dụng.');
            return;
        }

        if ($coupon->max_uses !== null && $coupon->used >= $coupon->max_uses) {
            session()->flash('error_message', 'Mã giảm giá đã hết lượt sử dụng.');
            return;
        }

        if ($coupon->user_id !== null) {
            if (!Auth::check() || Auth::id() !== $coupon->user_id) {
                session()->flash('error_message', 'Mã giảm giá này chỉ áp dụng cho người dùng cụ thể.');
                return;
            }
        }

        session()->put('coupon', [
            'coupon_code' => $coupon->coupon_code,
            'coupon_type' => $coupon->coupon_type,
            'coupon_value' => $coupon->coupon_value,
            'cart_value' => $coupon->cart_value,
            'end_date' => $coupon->end_date,
        ]);

        $coupon->increment('used');
        $this->calculateDiscount();
        session()->flash('success_message', 'Mã giảm giá đã được áp dụng.');
    }

    public function removeCoupon()
    {
        if (session()->has('coupon')) {
            session()->forget('coupon');
            $this->discount = 0;
            $this->subtotalAfterDiscount = Cart::instance('cart')->subtotal();
            $this->totalAfterDiscount = $this->subtotalAfterDiscount + $this->shippingFee;
            session()->flash('success_message', 'Mã giảm giá đã được hủy.');
        }
    }

    public function toggleShowAllCoupons()
    {
        $this->showAllCoupons = !$this->showAllCoupons;
    }

    public function calculateDiscount()
    {
        if (session()->has('coupon')) {
            $coupon = session()->get('coupon');
            $this->discount = $coupon['coupon_type'] == 'fixed' 
                ? $coupon['coupon_value'] 
                : (Cart::instance('cart')->subtotal() * $coupon['coupon_value']) / 100;

            $this->discount = min($this->discount, Cart::instance('cart')->subtotal());
            $this->subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $this->discount;
            $this->totalAfterDiscount = $this->subtotalAfterDiscount + $this->shippingFee;
        } else {
            $this->discount = 0;
            $this->subtotalAfterDiscount = Cart::instance('cart')->subtotal();
            $this->totalAfterDiscount = $this->subtotalAfterDiscount + $this->shippingFee;
        }
    }

    public function setAmountForCheckout()
    {
        if (session()->has('coupon')) {
            session()->put('checkout', [
                'discount' => $this->discount,
                'subtotal' => $this->subtotalAfterDiscount,
                'total' => $this->totalAfterDiscount,
                'shipping_fee' => $this->shippingFee,
            ]);
        } else {
            session()->put('checkout', [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'total' => Cart::instance('cart')->subtotal() + $this->shippingFee,
                'shipping_fee' => $this->shippingFee,
            ]);
        }
    }

    public function Store($product_id, $product_name, $product_price)
    {
        if (!session()->has('coupon')) {
            session()->flash('error_message', 'Vui lòng áp dụng mã giảm giá trước khi thêm sản phẩm.');
            return;
        }

        Cart::instance('cart')->add($product_id, $product_name, 1, $product_price)->associate('App\Models\Product');
        $this->updateShippingFee();
        $this->loadCoupons();
        $this->dispatch('refreshComponent')->to('carticon-component');
        flash('Mặt hàng đã được thêm vào giỏ hàng.');
    }

    public function render()
    {
        if (Auth::check()) {
            Cart::instance('cart')->store(Auth::user()->email);
            Cart::instance('wishlist')->store(Auth::user()->email);
        }

        if (session()->has('coupon')) {
            if (Cart::instance('cart')->subtotal() < session()->get('coupon')['cart_value']) {
                session()->forget('coupon');
                session()->flash('error_message', 'Giỏ hàng không đủ điều kiện cho mã giảm giá.');
            } else {
                $this->calculateDiscount();
            }
        }

        $this->updateShippingFee();
        $this->findBestCoupon();
        $this->setAmountForCheckout();
        $products = Product::inRandomOrder()->take(12)->get();

        return view('livewire.cart-component', [
            'products' => $products,
            'applicableCoupons' => $this->applicableCoupons,
        ]);
    }
}