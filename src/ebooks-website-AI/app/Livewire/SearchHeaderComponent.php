<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SearchHeaderComponent extends Component
{
    public $search = '';
    public $searchSuggestions = [];
    public $popularProducts = [];
    public $noResultsMessage = '';

    public function mount()
    {
        $this->fill(request()->only('search'));
        $this->loadPopularProducts();
    }

    private function loadPopularProducts()
    {
        $this->popularProducts = Cache::remember('popular_products', 3600, function () {
            $sales = DB::table('order_items')
                ->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
                ->groupBy('product_id');

            $products = Product::select('products.*')
                ->with('category')
                ->leftJoinSub($sales, 'sales', function ($join) {
                    $join->on('products.id', '=', 'sales.product_id');
                })
                ->orderByRaw('COALESCE(sales.sold_quantity, 0) DESC')
                ->take(3)
                ->get();

            return $products->map(fn($product) => [
                'name' => $product->name ?? 'Unnamed Product',
                'author' => $product->author ?? 'Unknown Author',
                'category' => $product->category->name ?? 'N/A',
                'image' => $product->image ?? 'default-image.jpg',
                'slug' => $product->slug ?? Str::slug($product->name),
               
            ])->toArray();
        });
    }

    public function updatedSearch($value)
    {
        $trimmed = trim($value);

        // Lưu lịch sử tìm kiếm (giữ lại nhưng không hiển thị)
        if (!empty($trimmed)) {
            if (Auth::check()) {
                SearchHistory::updateOrCreate(
                    ['user_id' => Auth::id(), 'keyword' => $trimmed],
                    ['searched_at' => now()]
                );
            } else {
                $recent = Session::get('recent_searches', []);
                $recent = array_filter($recent, fn($item) => $item['keyword'] !== $trimmed);
                $recent[] = ['keyword' => $trimmed, 'searched_at' => now()->toDateTimeString()];
                usort($recent, fn($a, $b) => strtotime($b['searched_at']) - strtotime($a['searched_at']));
                if (count($recent) > 5) {
                    $recent = array_slice($recent, 0, 5);
                }
                Session::put('recent_searches', $recent);
            }
        }

        // Tìm kiếm gợi ý sách dựa trên bất kỳ từ khóa nào
        if (!empty($trimmed)) {
            $products = Product::where(function ($query) use ($trimmed) {
                $query->where('name', 'like', "%{$trimmed}%")
                      ->orWhere('author', 'like', "%{$trimmed}%")
                      ->orWhere('description', 'like', "%{$trimmed}%")
                      ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$trimmed}%"));
            })
            ->orderByRaw("CASE 
                WHEN name LIKE ? THEN 1 
                WHEN author LIKE ? THEN 2 
                WHEN description LIKE ? THEN 3 
                ELSE 4 
                END", ["%{$trimmed}%", "%{$trimmed}%", "%{$trimmed}%"])
            ->take(5)
            ->get();

            if ($products->isNotEmpty()) {
                $this->searchSuggestions = $products->map(fn($product) => [
                    'name' => $product->name ?? 'Unnamed Product',
                    'author' => $product->author ?? 'Unknown Author',
                    'category' => $product->category->name ?? 'N/A',
                    'image' => $product->image ?? 'default-image.jpg',
                    'slug' => $product->slug ?? Str::slug($product->name),
                   
                ])->toArray();
                $this->noResultsMessage = '';
            } else {
                $this->searchSuggestions = [];
                $this->noResultsMessage = 'Không tìm thấy kết quả nào.';
            }
        } else {
            $this->searchSuggestions = [];
            $this->noResultsMessage = '';
        }

        $this->dispatch('updateSuggestions');
    }

    public function render()
    {
        return view('livewire.search-header-component');
    }
}