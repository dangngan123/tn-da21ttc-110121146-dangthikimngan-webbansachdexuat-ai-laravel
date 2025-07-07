<?php

namespace App\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WishlistComponent extends Component
{
    public function loadWishlist()
    {
        if (Auth::check()) {
            // Lấy danh sách yêu thích từ bảng wishlist
            $wishlistItemsFromDb = Wishlist::with('product')
                ->where('user_id', Auth::id())
                ->get();

            // Lấy danh sách từ Cart
            $cartItems = Cart::instance('wishlist')->content();

            // Đồng bộ Cart với bảng wishlist
            // Xóa các mục trong Cart nếu không tồn tại trong bảng wishlist
            foreach ($cartItems as $cartItem) {
                $existsInDb = $wishlistItemsFromDb->contains('product_id', $cartItem->id);
                if (!$existsInDb) {
                    Cart::instance('wishlist')->remove($cartItem->rowId);
                    Log::info('Removed invalid wishlist item from Cart', [
                        'product_id' => $cartItem->id,
                        'rowId' => $cartItem->rowId,
                    ]);
                }
            }

            // Thêm các mục từ bảng wishlist vào Cart nếu chưa có
            foreach ($wishlistItemsFromDb as $wishlistItem) {
                if ($wishlistItem->product) {
                    $existsInCart = $cartItems->contains('id', $wishlistItem->product_id);
                    if (!$existsInCart) {
                        Cart::instance('wishlist')->add([
                            'id' => $wishlistItem->product->id,
                            'name' => $wishlistItem->product->name,
                            'qty' => 1,
                            'price' => $wishlistItem->product->sale_price ?? $wishlistItem->product->regular_price,
                            'options' => [
                                'image' => $wishlistItem->product->image,
                            ],
                        ])->associate('App\Models\Product');
                        Log::info('Added wishlist item to Cart', [
                            'product_id' => $wishlistItem->product_id,
                        ]);
                    }
                }
            }

            // Trả về danh sách wishlist items từ Cart
            $wishlistItems = Cart::instance('wishlist')->content();
            Log::info('Wishlist items loaded', ['items' => $wishlistItems->toArray()]);

            return $wishlistItems;
        }

        Log::info('User not authenticated, wishlist is empty');
        return collect();
    }

    // Hàm tính số lượng sản phẩm đã bán
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
        // Load wishlist items directly in render
        $wishlistItems = $this->loadWishlist();

        // Tạo mảng dữ liệu để truyền vào Blade
        $wishlistData = [];
        foreach ($wishlistItems as $item) {
            if ($item->model) {
                $soldQuantity = $this->getSoldQuantity($item->model->id);
                $wishlistData[] = [
                    'rowId' => $item->rowId,
                    'model' => $item->model,
                    'sold_quantity' => $soldQuantity,
                ];
                Log::info('Prepared wishlist item for rendering', [
                    'product_id' => $item->model->id,
                    'sold_quantity' => $soldQuantity,
                ]);
            }
        }

        return view('livewire.wishlist-component', [
            'wishlistData' => $wishlistData,
        ]);
    }
}
