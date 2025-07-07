<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ShopComponent extends Component
{
    use WithPagination;

    public $pagesize = 24;
    public $selectedAges = [];
    public $selectedPublishers = [];
    public $showAllPublishers = false;
    public $orderBy = 'Mặc định';
    protected $paginationTheme = 'bootstrap';
    public $min_price = 0;
    public $max_price = 700;
    public $priceRange = [];

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
        //    flash()->success( 'Đã hiển thị toàn bộ nhà xuất bản.');
    }

    public function collapsePublisherList()
    {
        $this->showAllPublishers = false;
        // flash()->success('Đã thu gọn danh sách nhà xuất bản.');
    }

    public function mount()
    {
        $this->priceRange = [];
        $this->selectedPublishers = [];
        $this->showAllPublishers = false;
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


    public function previousPage()
    {
        $this->setPage(max(1, $this->page - 1));
    }

    public function nextPage()
    {
        $this->setPage(min($this->page + 1, $this->products->lastPage()));
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }

    public function render()
    {
        $categories = Category::get();
        $publishers = Product::select('publisher')->distinct()->pluck('publisher')->filter()->values();
        $query = Product::query();

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

        if (!empty($this->selectedAges)) {
            $query->where(function ($q) {
                foreach ($this->selectedAges as $index => $ageRange) {
                    list($minAge, $maxAge) = explode('-', $ageRange);
                    $minAge = (int)$minAge;
                    $maxAge = (int)$maxAge;
                    $q->when($index === 0, fn($q) => $q->whereBetween('age', [$minAge, $maxAge]))
                        ->orWhereBetween('age', [$minAge, $maxAge]);
                }
            });
        }

        if (!empty($this->selectedPublishers)) {
            $query->whereIn('publisher', $this->selectedPublishers);
        }

        if ($this->orderBy == 'Giá thấp') {
            $query->whereBetween('sale_price', [$this->min_price, $this->max_price])
                ->orderBy('sale_price', 'ASC');
        } elseif ($this->orderBy == 'Giá cao') {
            $query->whereBetween('sale_price', [$this->min_price, $this->max_price])
                ->orderBy('sale_price', 'DESC');
        } elseif ($this->orderBy == 'Sản phẩm mới') {
            $query->whereBetween('sale_price', [$this->min_price, $this->max_price])
                ->orderBy('created_at', 'DESC');
        } else {
            $query->whereBetween('sale_price', [$this->min_price, $this->max_price]);
        }

        $products = $query->paginate($this->pagesize);
        $nproducts = Product::latest()->take(3)->get();

        foreach ($products as $product) {
            $product->sold_quantity = $this->getSoldQuantity($product->id);
        }
        foreach ($nproducts as $nproduct) {
            $nproduct->sold_quantity = $this->getSoldQuantity($nproduct->id);
        }

        $reviews = Review::all();




        return view('livewire.shop-component', [
            'categories' => $categories,
            'publishers' => $publishers,
            'products' => $products,
            'nproducts' => $nproducts,
            'reviews' => $reviews
        ]);
    }
}
