<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class CategoryComponent extends Component
{
    use WithPagination;

    public $slug;
    public $selectedAges = [];
    public $selectedPublishers = [];
    public $showAllPublishers = false;
    public $hasMorePublishers = false; // Thêm thuộc tính để kiểm tra xem có nhiều nhà xuất bản hơn 8 không
    public $pagesize = 24;
    public $orderBy = 'Mặc định';
    protected $paginationTheme = 'bootstrap';
    public $min_price = 0;
    public $max_price = 700;
    public $priceRange = [];
    public $totalSoldQuantity = 0;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->priceRange = [];
        $this->selectedPublishers = [];
        $this->showAllPublishers = false;
    }

    public function changepageSize($size)
    {
        Log::info('changepageSize called with size: ' . $size);
        $this->pagesize = $size;
        $this->resetPage();
    }

    public function changeOrderBy($order)
    {
        Log::info('changeOrderBy called with order: ' . $order);
        $this->orderBy = $order;
        $this->resetPage();
    }

    public function expandPublisherList()
    {
        $this->showAllPublishers = true;
        session()->flash('message', 'Đã hiển thị toàn bộ nhà xuất bản.');
    }

    public function collapsePublisherList()
    {
        $this->showAllPublishers = false;
        session()->flash('message', 'Đã thu gọn danh sách nhà xuất bản.');
    }

    public function updatedPriceRange()
    {
        Log::info('Price Range Updated:', $this->priceRange);
        $this->resetPage();
    }

    public function updatedSelectedAges()
    {
        Log::info('Selected Ages Updated:', $this->selectedAges);
        $this->resetPage();
    }

    public function updatedSelectedPublishers()
    {
        Log::info('Selected Publishers Updated:', $this->selectedPublishers);
        $this->resetPage();
    }

    public function getSoldQuantity($productId)
    {
        return DB::table('order_items')
            ->where('product_id', $productId)
            ->sum('quantity');
    }

    public function render()
    {
        $categories = Category::get();
        $category = Category::where('slug', $this->slug)->first();

        // Lấy danh sách nhà xuất bản duy nhất trong danh mục
        $publishers = Product::where('category_id', $category->id)
            ->select('publisher')
            ->distinct()
            ->pluck('publisher')
            ->filter()
            ->values();
        Log::info('Number of publishers: ' . $publishers->count());

        // Tính toán xem có nhiều nhà xuất bản hơn 8 không
        $this->hasMorePublishers = $publishers->count() > 8;

        // Tạo query sản phẩm
        $query = Product::where('category_id', $category->id)
            ->whereBetween('sale_price', [$this->min_price, $this->max_price]);

        // Lọc theo giá
        if (!empty($this->priceRange)) {
            $query->where(function ($q) {
                foreach ($this->priceRange as $range) {
                    list($minPrice, $maxPrice) = explode('-', $range);
                    $minPrice = (int)$minPrice;
                    $maxPrice = (int)$maxPrice;
                    $q->orWhere(function ($query) use ($minPrice, $maxPrice) {
                        $query->where('sale_price', '>=', $minPrice)
                            ->where('sale_price', '<=', $maxPrice);
                    });
                }
            });
        }

        // Lọc theo độ tuổi
        if (!empty($this->selectedAges)) {
            $query->where(function ($q) {
                foreach ($this->selectedAges as $ageRange) {
                    list($minAge, $maxAge) = explode('-', $ageRange);
                    $q->orWhereBetween('age', [$minAge, $maxAge]);
                }
            });
        }

        // Lọc theo nhà xuất bản
        if (!empty($this->selectedPublishers)) {
            $query->whereIn('publisher', $this->selectedPublishers);
        }

        // Sắp xếp
        if ($this->orderBy == 'Giá thấp') {
            $query->orderBy('sale_price', 'ASC');
        } elseif ($this->orderBy == 'Giá cao') {
            $query->orderBy('sale_price', 'DESC');
        } elseif ($this->orderBy == 'Sản phẩm mới') {
            $query->orderBy('created_at', 'DESC');
        }

        $products = $query->paginate($this->pagesize);
        $nproducts = Product::latest()->take(3)->get();
        $cateroryName = $category->name;
        $reviews = Review::all();

        // Tính sold_quantity cho từng sản phẩm và tổng
        $this->totalSoldQuantity = 0;
        foreach ($products as $product) {
            $product->sold_quantity = $this->getSoldQuantity($product->id);
            $this->totalSoldQuantity += $product->sold_quantity;
        }

        foreach ($nproducts as $nproduct) {
            $nproduct->sold_quantity = $this->getSoldQuantity($nproduct->id);
        }

        return view('livewire.category-component', [
            'categories' => $categories,
            'publishers' => $publishers,
            'products' => $products,
            'nproducts' => $nproducts,
            'cateroryName' => $cateroryName,
            'reviews' => $reviews,
            'totalSoldQuantity' => $this->totalSoldQuantity,
        ]);
    }
}