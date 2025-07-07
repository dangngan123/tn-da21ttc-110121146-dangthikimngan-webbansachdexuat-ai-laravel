<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\SearchHistory;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SearchComponent extends Component
{
    use WithPagination;

    public $pagesize = 24;
    public $orderBy = 'Mặc định';
    public $priceRange = [];
    public $min_price = 0;
    public $max_price = 700;
    public $search;
    public $search_term;
    public $recentSearches = [];
    public $noResultsMessage = '';
    public $selectedAges = [];
    public $selectedPublishers = [];
    public $publishers = [];
    public $showAllPublishers = false;
    public $hasMorePublishers = false;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'pagesize' => ['except' => 12],
        'orderBy' => ['except' => 'Mặc định'],
        'priceRange' => ['except' => []],
        'selectedAges' => ['except' => []],
        'selectedPublishers' => ['except' => []],
    ];

    public function mount()
    {
        $this->fill(request()->only('search'));
        Log::info('Mount called', ['search' => $this->search, 'url' => request()->fullUrl()]);

        if (empty($this->search) && request()->query('page') === null) {
            Log::info('Redirecting to home due to empty search and no page');
            return redirect()->route('home');
        }

        $this->search_term = !empty($this->search) ? '%' . $this->search . '%' : '';
        $this->saveSearchHistory();
        $this->loadRecentSearches();
        $this->loadPublishers();
    }

    public function updatedSearch()
    {
        $this->search_term = !empty($this->search) ? '%' . $this->search . '%' : '';
        Log::info('Search updated', ['search' => $this->search, 'search_term' => $this->search_term]);
        $this->setPage(1);
        $this->saveSearchHistory();
        $this->loadRecentSearches();
    }

    protected function saveSearchHistory()
    {
        if (Auth::check() && !empty($this->search)) {
            try {
                SearchHistory::create([
                    'user_id' => Auth::id(),
                    'keyword' => trim($this->search),
                    'searched_at' => now(),
                ]);
                Log::info('Search history recorded', [
                    'user_id' => Auth::id(),
                    'keyword' => $this->search,
                ]);
            } catch (\Exception $e) {
                Log::error('Error recording search history', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'keyword' => $this->search,
                ]);
            }
        }
    }

    protected function loadRecentSearches()
    {
        if (Auth::check()) {
            $this->recentSearches = SearchHistory::where('user_id', Auth::id())
                ->orderBy('searched_at', 'desc')
                ->take(5)
                ->pluck('keyword')
                ->toArray();
        } else {
            $this->recentSearches = [];
        }
    }

    protected function loadPublishers()
    {
        $publishers = Product::select('publisher')
            ->whereNotNull('publisher')
            ->distinct()
            ->orderBy('publisher')
            ->pluck('publisher')
            ->toArray();

        $this->publishers = $publishers;
        $this->hasMorePublishers = count($publishers) > 8;
    }

    public function clearRecentSearches()
    {
        Log::info('clearRecentSearches called', [
            'user_id' => Auth::id() ?? 'Guest',
            'recent_searches_before' => $this->recentSearches,
        ]);

        $this->recentSearches = [];
        $this->noResultsMessage = 'Lịch sử tìm kiếm đã được xóa.';

        Log::info('clearRecentSearches completed', [
            'recent_searches' => $this->recentSearches,
            'no_results_message' => $this->noResultsMessage,
        ]);
    }

    public function removeRecentSearch($keyword)
    {
        $trimmed = trim($keyword);

        Log::info('removeRecentSearch called', [
            'user_id' => Auth::id() ?? 'Guest',
            'keyword' => $trimmed,
            'recent_searches_before' => $this->recentSearches,
        ]);

        $this->recentSearches = array_filter($this->recentSearches, fn($item) => $item !== $trimmed);
        $this->noResultsMessage = 'Đã xóa từ khóa tìm kiếm.';

        Log::info('removeRecentSearch completed', [
            'recent_searches' => $this->recentSearches,
            'no_results_message' => $this->noResultsMessage,
        ]);
    }

    public function changepageSize($size)
    {
        $this->pagesize = $size;
        $this->setPage(1);
    }

    public function changeOrderBy($order)
    {
        $this->orderBy = $order;
        $this->setPage(1);
    }

    public function updatedPriceRange()
    {
        Log::info('Price Range Updated:', $this->priceRange);
        $this->setPage(1);
    }

    public function updatedSelectedAges()
    {
        Log::info('Selected Ages Updated:', $this->selectedAges);
        $this->setPage(1);
    }

    public function updatedSelectedPublishers()
    {
        Log::info('Selected Publishers Updated:', $this->selectedPublishers);
        $this->setPage(1);
    }

    public function expandPublisherList()
    {
        $this->showAllPublishers = true;
    }

    public function collapsePublisherList()
    {
        $this->showAllPublishers = false;
    }

    public function resetMessage()
    {
        $this->noResultsMessage = '';
    }

    public function getSoldQuantity($productId)
    {
        return DB::table('order_items')
            ->where('product_id', $productId)
            ->sum('quantity');
    }

    public function render()
    {
        Log::info('Rendering with search term:', [
            'search_term' => $this->search_term,
            'url' => request()->fullUrl(),
        ]);

        $categories = Category::get();
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

        $query->where(function ($q) {
            if ($this->search_term) {
                $q->where('name', 'like', $this->search_term)
                    ->orWhere('author', 'like', $this->search_term)
                    ->orWhere('publisher', 'like', $this->search_term);
            }
        });

        switch ($this->orderBy) {
            case 'Giá thấp':
                $query->orderBy('sale_price', 'ASC');
                break;
            case 'Giá cao':
                $query->orderBy('sale_price', 'DESC');
                break;
            case 'Sản phẩm mới':
                $query->orderBy('created_at', 'DESC');
                break;
            default:
                break;
        }

        $query->whereBetween('sale_price', [$this->min_price, $this->max_price]);

        $products = $query->paginate($this->pagesize);

        $nproducts = Product::latest()->take(3)->get();

        // Thêm số lượng đã bán cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->sold_quantity = $this->getSoldQuantity($product->id);
        }
        foreach ($nproducts as $nproduct) {
            $nproduct->sold_quantity = $this->getSoldQuantity($nproduct->id);
        }

        return view('livewire.search-component', [
            'categories' => $categories,
            'products' => $products,
            'nproducts' => $nproducts,
            'recentSearches' => $this->recentSearches,
            'publishers' => $this->publishers,
        ]);
    }
}