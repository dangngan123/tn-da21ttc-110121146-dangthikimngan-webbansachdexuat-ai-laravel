<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\UserInteraction;
use App\Models\Review;
use App\Models\OrderItem;
use App\Models\Coupon;
use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DetailsComponent extends Component
{
    public $slug;
    public $qty;
    public $publisher;
    public $author;
    public $age;
    public $quantity;
    public $name;
    public $regular_price;
    public $sale_price;
    public $order_item_id;
    public $wishlistItems;
    public $product;
    public $selectedCoupon = null;
    public $showAllCoupons = false;
    public $soldQuantity = 0; // Thêm thuộc tính public để lưu sold_quantity

    public function mount($slug)
    {
        Log::info('DetailsComponent mounted', ['slug' => $slug]);
        $this->slug = $slug;
        $this->qty = 1;

        $this->product = Product::where('slug', $this->slug)
            ->orWhere('name', 'like', "%{$this->slug}%")
            ->orWhere('slug', 'like', "%" . \Illuminate\Support\Str::ascii($this->slug) . "%")
            ->first();

        if (!$this->product) {
            Log::error('Product not found for slug', ['slug' => $this->slug]);
            flash()->error('Sản phẩm không tồn tại! Thử tìm "Hai Số Phận Tái Bản" nhé.');
            return redirect()->route('home');
        }

        if ($this->product->slug !== $this->slug) {
            Log::info('Redirecting to correct slug', ['from' => $this->slug, 'to' => $this->product->slug]);
            return redirect()->route('details', ['slug' => $this->product->slug]);
        }

        $this->product->load('category');
        $this->recordView();
        $this->loadWishlist();
    }
    public function recordView()
    {
        if (Auth::check() && $this->product) {
            try {
                UserInteraction::create([
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id,
                    'interaction_type' => 'click',
                    'interaction_value' => 2,
                    'created_at' => now(),
                ]);
                Log::info('User interaction recorded (click)', [
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Error recording user interaction (click)', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'product_id' => $this->product->id ?? null,
                    'slug' => $this->slug,
                ]);
            }
        } else {
            Log::warning('Cannot record user interaction: User not authenticated or product is null', [
                'user_id' => Auth::id(),
                'slug' => $this->slug,
            ]);
        }
    }

    public function addToCart($product_id, $product_name, $product_price)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return $this->showAdminAlert();
        }

        $this->Store($product_id, $product_name, $product_price);
    }

    public function Store($product_id, $product_name, $product_price)
    {
        try {
            $cartItem = Cart::instance('cart')->add($product_id, $product_name, $this->qty, $product_price)->associate('App\Models\Product');

            if (Auth::check()) {
                $product = Product::find($product_id);
                if ($product) {
                    UserInteraction::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product_id,
                        'interaction_type' => 'add_to_cart',
                        'interaction_value' => 3,
                        'created_at' => now(),
                    ]);
                    Log::info('User interaction recorded (add_to_cart)', [
                        'user_id' => Auth::id(),
                        'product_id' => $product_id,
                        'interaction_value' => 3,
                        'cart_row_id' => $cartItem->rowId,
                    ]);
                } else {
                    Log::warning('Product not found for add_to_cart', [
                        'product_id' => $product_id,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            flash()->success('Mặt hàng đã được thêm vào giỏ hàng.');
            $this->dispatch('refreshComponent')->to('carticon-component');
        } catch (\Exception $e) {
            Log::error('Error adding to cart', [
                'error' => $e->getMessage(),
                'product_id' => $product_id,
                'user_id' => Auth::id(),
            ]);
            flash()->error('Có lỗi khi thêm vào giỏ hàng: ' . $e->getMessage());
        }
    }

    public function loadWishlist()
    {
        if (Auth::check()) {
            $this->wishlistItems = Wishlist::with('product')
                ->where('user_id', Auth::id())
                ->get();
            Log::info('Wishlist items loaded in DetailsComponent', ['items' => $this->wishlistItems->toArray()]);
        } else {
            $this->wishlistItems = collect();
        }
    }

    public function addtoWishlist($productId, $productName, $productPrice)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $product = Product::find($productId);
            if (!$product) {
                Log::error('Product not found', ['product_id' => $productId]);
                flash()->error('Sản phẩm không tồn tại!');
                return;
            }

            $exists = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->exists();

            if (!$exists) {
                $wishlist = Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                ]);
                Log::info('Wishlist item created', ['wishlist' => $wishlist->toArray()]);

                // Thêm vào Cart và liên kết với model Product
                $cartItem = Cart::instance('wishlist')->add([
                    'id' => $product->id,
                    'name' => $product->name,
                    'qty' => 1,
                    'price' => $productPrice ?? $product->regular_price,
                    'options' => ['image' => $product->image],
                ])->associate('App\Models\Product');

                Log::info('Added to wishlist cart', [
                    'cart_item' => $cartItem->toArray(),
                    'product_id' => $productId,
                ]);

                $this->loadWishlist();
                flash()->success("Đã thêm '$productName' vào danh sách yêu thích!");
            } else {
                flash()->error("'$productName' đã có trong danh sách yêu thích!");
            }
        } catch (\Exception $e) {
            Log::error('Error adding to wishlist', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id(),
            ]);
            flash()->error('Có lỗi khi thêm vào danh sách yêu thích: ' . $e->getMessage());
        }
    }
    public function removefromWishlist($productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $deleted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->delete();

            Log::info('Wishlist item deleted from database', [
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'deleted_rows' => $deleted,
            ]);

            $cartItem = Cart::instance('wishlist')->search(function ($cartItem) use ($productId) {
                return $cartItem->id == $productId;
            })->first();

            if ($cartItem) {
                Cart::instance('wishlist')->remove($cartItem->rowId);
                Log::info('Wishlist item deleted from cart', ['product_id' => $productId]);
            }

            $this->loadWishlist();
            flash()->success('Đã xóa sản phẩm khỏi danh sách yêu thích!');
            $this->dispatch('refreshComponent')->to('wishlist-icon-component');
        } catch (\Exception $e) {
            Log::error('Error removing from wishlist', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id(),
            ]);
            flash()->error('Có lỗi khi xóa khỏi danh sách yêu thích: ' . $e->getMessage());
        }
    }

    public function QtyDecrease()
    {
        if ($this->qty > 1) {
            $this->qty--;
        } else {
            flash()->error('Không thể giảm số lượng thấp hơn 1!');
        }
    }

    public function QtyIncrease($quantity)
    {
        if ($this->qty < $quantity) {
            $this->qty++;
        } else {
            flash()->error('Số lượng không đủ!');
        }
    }

    public function updateQty($value, $quantity)
    {
        $value = (int)$value;

        if ($value < 1) {
            $this->qty = 1;
            flash()->error('Số lượng không thể nhỏ hơn 1!');
            return;
        }

        if ($value > $quantity) {
            $this->qty = $quantity;
            flash()->error('Số lượng không đủ! Số lượng vượt quá tồn kho (' . $quantity . ')!');
            return;
        }

        $this->qty = $value;
    }

    public function ProductQuickView($id)
    {
        $this->dispatch('product-quick-view');
        $product = Product::where('id', $id)->first();
        if ($product) {
            $this->name = $product->name;
            $this->regular_price = $product->regular_price;
            $this->sale_price = $product->sale_price;
        }
    }

    public function showCouponDetails($couponId)
    {
        $this->selectedCoupon = Coupon::find($couponId);
    }

    public function closeCouponDetails()
    {
        $this->selectedCoupon = null;
        Log::info('Selected coupon has been set to null');
    }

    public function toggleShowAll()
    {
        $this->showAllCoupons = !$this->showAllCoupons;
        $this->selectedCoupon = null;
    }

    public function applyCoupon($couponId)
    {
        if (session()->has('coupon')) {
            flash()->error('Mã giảm giá đã được áp dụng cho đơn hàng này.');
            return;
        }

        $coupon = Coupon::where('id', $couponId)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            flash()->error('Mã giảm giá không hợp lệ, đã hết hạn hoặc không đủ điều kiện áp dụng.');
            return;
        }

        if ($coupon->max_uses !== null && $coupon->used >= $coupon->max_uses) {
            session()->flash('error_message', 'Mã giảm giá đã hết lượt sử dụng.');
            return;
        }

        if ($coupon->user_id !== null) {
            if (!Auth::check() || Auth::id() !== $coupon->user_id) {
                flash()->error('Mã giảm giá này chỉ áp dụng cho người dùng cụ thể.');
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
        flash()->success('Mã giảm giá đã được áp dụng.');
    }

    public function showAdminAlert()
    {
        flash()->error('Lỗi: Admin không thể thêm sản phẩm vào giỏ hàng!');
    }

    public function reply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
            'admin_reply_at' => now()
        ]);

        return back()->with('success', 'Phản hồi đã được gửi thành công');
    }

    public function getSoldQuantity($productId)
    {
        $soldQuantity = DB::table('order_items')
            ->where('product_id', $productId)
            ->sum('quantity');

        Log::info('Sold Quantity Calculated', [
            'product_id' => $productId,
            'sold_quantity' => $soldQuantity
        ]);

        return $soldQuantity;
    }

    public function render()
    {
        $product = Product::where("slug", $this->slug)->first();

        if (!$product) {
            Log::error('Product not found in render', ['slug' => $this->slug]);
            flash()->error('Sản phẩm không tồn tại!');
            return view('livewire.details-component', [
                'product' => null,
                'rproducts' => collect(),
                'nproducts' => collect(),
                'categories' => collect(),
                'images' => [],
                'qproducts' => collect(),
                'reviews' => collect(),
                'ordersItems' => null,
                'coupons' => collect(),
            ]);
        }

        $image = $product->image;
        $images = json_decode($product->images) ?? [];
        array_splice($images, 0, 0, $image);

        $rproducts = Product::where("category_id", $product->category_id)->get();
        $nproducts = Product::latest()->take(3)->get();
        $categories = Category::get();

        $couponQuery = Coupon::where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereColumn('used', '<', 'max_uses');
            });

        if (Auth::check()) {
            $couponQuery->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', Auth::id());
            });
        } else {
            $couponQuery->whereNull('user_id');
        }

        $coupons = $this->showAllCoupons ? $couponQuery->get() : $couponQuery->take(5)->get();

        $qproducts = Product::inRandomOrder()->take(4)->get();

        $orderItems = OrderItem::where('product_id', $product->id)->first();

        $reviews = Review::whereHas('orderItem', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->with('user')->get();

        $this->soldQuantity = $this->getSoldQuantity($product->id) ?? 0;

        $rproductIds = $rproducts->pluck('id')->toArray();
        if (!empty($rproductIds)) {
            $soldQuantities = DB::table('order_items')
                ->whereIn('product_id', $rproductIds)
                ->groupBy('product_id')
                ->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
                ->pluck('sold_quantity', 'product_id');

            foreach ($rproducts as $rproduct) {
                $rproduct->sold_quantity = $soldQuantities[$rproduct->id] ?? 0;
            }
        }

        return view('livewire.details-component', [
            'product' => $product,
            'rproducts' => $rproducts,
            'nproducts' => $nproducts,
            'categories' => $categories,
            'images' => $images,
            'qproducts' => $qproducts,
            'reviews' => $reviews,
            'ordersItems' => $orderItems,
            'coupons' => $coupons,
            'soldQuantity' => $this->soldQuantity,
        ]);
    }
}
