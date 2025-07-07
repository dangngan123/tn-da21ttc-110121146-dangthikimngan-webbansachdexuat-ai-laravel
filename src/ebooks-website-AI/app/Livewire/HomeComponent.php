<?php

namespace App\Livewire;

use App\Models\Slider;
use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use App\Models\Saletimer;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeComponent extends Component
{
    public $count = 24;
    public $recommendations; // Biến để chứa danh sách gợi ý
    // Thêm các thuộc tính trọng số với giá trị mặc định
    public $alpha = 0.4;
    public $beta = 0.3;
    public $gamma = 0.15;
    public $delta = 0.1;
    public $epsilon = 0.05;

    public function mount()
    {
        // Gọi API FastAPI để lấy gợi ý cho người dùng hiện tại
        $this->loadRecommendations();
    }

    public function store($product_id, $product_name, $product_price)
    {
        Cart::instance('cart')->add($product_id, $product_name, 1, $product_price)->associate('App\Models\Product');
        $this->dispatch('refreshComponent')->to('carticon-component');
        flash('Mặt hàng đã được thêm vào giỏ hàng.');
    }

    public function loadMore()
    {
        $this->count += 12;
    }

    public function showAdminAlert()
    {
        flash()->error('Lỗi: Admin không thể thêm sản phẩm vào giỏ hàng!');
    }

    // Phương thức lấy số lượng đã bán từ order_items
    public function getSoldQuantity($productId)
    {
        return DB::table('order_items')
            ->where('product_id', $productId)
            ->sum('quantity');
    }

    // Phương thức gọi API FastAPI để lấy gợi ý với trọng số tùy chỉnh
    public function loadRecommendations()
    {
        $userId = Auth::check() ? Auth::id() : 0; // Lấy user_id nếu đã đăng nhập, nếu không thì 0
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/recommend'; // Đường dẫn API

        Log::info("Gọi API gợi ý với URL: {$apiUrl}, user_id: {$userId}, trọng số: alpha={$this->alpha}, beta={$this->beta}, gamma={$this->gamma}, delta={$this->delta}, epsilon={$this->epsilon} tại " . Carbon::now('Asia/Ho_Chi_Minh'));

        try {
            $response = Http::timeout(30)->post($apiUrl, [
                'user_id' => $userId,
                'n_items' => 10, // Số lượng gợi ý
                'method' => 'hybrid', // Sử dụng phương pháp Hybrid
                'alpha' => floatval($this->alpha),      // Trọng số User-based SVD
                'beta' => floatval($this->beta),        // Trọng số Item-based SVD
                'gamma' => floatval($this->gamma),      // Trọng số Content-based
                'delta' => floatval($this->delta),      // Trọng số ALS User-based
                'epsilon' => floatval($this->epsilon),  // Trọng số ALS Item-based
            ]);

            if ($response->successful()) {
                $recommendationsData = $response->json();
                Log::info("API trả về thành công: ", $recommendationsData);
                if (!empty($recommendationsData)) {
                    $productIds = array_column($recommendationsData, 'id');
                    $this->recommendations = Product::whereIn('id', $productIds)->get()->map(function ($product) {
                        $product->sold_quantity = $this->getSoldQuantity($product->id);
                        return $product;
                    });
                } else {
                    Log::warning("API trả về mảng rỗng, sử dụng fallback.");
                    $this->recommendations = $this->getFallbackRecommendations();
                }
            } else {
                Log::error('Lỗi khi gọi API gợi ý: ' . $response->body());
                $this->recommendations = $this->getFallbackRecommendations();
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API gợi ý: ' . $e->getMessage());
            $this->recommendations = $this->getFallbackRecommendations();
        }
    }

    // Phương thức fallback lấy sản phẩm phổ biến
    protected function getFallbackRecommendations()
    {
        Log::info("Sử dụng fallback: Lấy sản phẩm phổ biến.");
        return Product::orderBy('sold_count', 'desc')
            ->take(10)
            ->get()
            ->map(function ($product) {
                $product->sold_quantity = $this->getSoldQuantity($product->id);
                return $product;
            });
    }

    public function render()
    {
        $best_sellers = DB::table('products')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('products.id, SUM(order_items.quantity) as total')
            ->groupBy('products.id')
            ->orderBy('total', 'DESC')
            ->take(8)
            ->get();

        $bestproducts = new Collection();
        foreach ($best_sellers as $best_seller) {
            $product = Product::findOrFail($best_seller->id);
            $product->sold_quantity = $this->getSoldQuantity($product->id);
            $bestproducts->push($product);
        }

        $sliders = Slider::whereDate('start_date', '<=', Carbon::now('Asia/Ho_Chi_Minh')->toDateString())
            ->whereDate('end_date', '>=', Carbon::now('Asia/Ho_Chi_Minh')->toDateString())
            ->where('status', 1)
            ->where('type', 'slider')
            ->get();

        $categories = Category::where('status', 1)->get();
        $products = Product::limit($this->count)->get();
        $pcategories = Category::latest()->limit(8)->get();
        $nproducts = Product::latest()->limit(8)->get();

        $saletimerproducts = Product::whereBetween('sale_price', [50, 100])->limit(12)->get();
        $saletimer = Saletimer::find(1);

        if ($saletimer && $saletimer->sale_timer && $saletimer->start_date && Carbon::parse($saletimer->sale_timer)->isValid() && Carbon::parse($saletimer->start_date)->isValid()) {
            $saletimer->sale_timer = Carbon::parse($saletimer->sale_timer, 'Asia/Ho_Chi_Minh');
            $saletimer->start_date = Carbon::parse($saletimer->start_date, 'Asia/Ho_Chi_Minh');
        } else {
            $saletimer = null;
        }

        $productReviews = [];
        if (isset($product)) {
            $productReviews = Review::whereIn('order_item_id', function ($query) use ($product) {
                $query->select('id')
                    ->from('order_items')
                    ->where('product_id', $product->id);
            })->get();
        }

        foreach ($saletimerproducts as $product) {
            $product->sold_quantity = $this->getSoldQuantity($product->id);
        }

        foreach ($nproducts as $product) {
            $product->sold_quantity = $this->getSoldQuantity($product->id);
        }

        $uniqueProducts = collect();
        $saletimerproducts->each(function ($product) use ($uniqueProducts) {
            $uniqueProducts->put($product->id, $product);
        });
        $bestproducts->each(function ($product) use ($uniqueProducts) {
            $uniqueProducts->put($product->id, $product);
        });
        $nproducts->each(function ($product) use ($uniqueProducts) {
            $uniqueProducts->put($product->id, $product);
        });

        $totalSold = $uniqueProducts->sum(function ($product) {
            return $product->sold_quantity ?? 0;
        });

        return view('livewire.home-component', [
            'sliders' => $sliders,
            'categories' => $categories,
            'products' => $products,
            'pcategories' => $pcategories,
            'nproducts' => $nproducts,
            'bestproducts' => $bestproducts,
            'saletimerproducts' => $saletimerproducts,
            'saletimer' => $saletimer,
            'productReviews' => $productReviews,
            'totalSold' => $totalSold,
            'recommendations' => $this->recommendations,
            'alpha' => $this->alpha,
            'beta' => $this->beta,
            'gamma' => $this->gamma,
            'delta' => $this->delta,
            'epsilon' => $this->epsilon,
        ]);
    }
}