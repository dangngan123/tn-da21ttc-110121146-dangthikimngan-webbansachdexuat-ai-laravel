<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Validate;
use App\Models\Interaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;

class ManageRecommendationsComponent extends Component
{
    public $activeSection = 'terms';

    // Gợi ý sách
    #[Validate('required|integer|min:1')]
    public $user_id = 1;
    #[Validate('required|string|in:hybrid,als_user,als_item,user_based_svd,item_based_svd,content_based')]
    public $method = 'hybrid';
    #[Validate('required|integer|min:1|max:50')]
    public $n_items = 10;
    #[Validate('nullable|numeric|min:0|max:1')]
    public $alpha = 0.4;
    #[Validate('nullable|numeric|min:0|max:1')]
    public $beta = 0.3;
    #[Validate('nullable|numeric|min:0|max:1')]
    public $gamma = 0.15;
    #[Validate('nullable|numeric|min:0|max:1')]
    public $delta = 0.1;
    #[Validate('nullable|numeric|min:0|max:1')]
    public $epsilon = 0.05;
    public $recommendations = [];
    public $recError = '';

    // Tra cứu sản phẩm
    #[Validate('nullable|integer|min:1')]
    public $product_id = '';
    public $products = [];
    public $productDetails = null;
    public $productError = '';

    // Đánh giá mô hình
    #[Validate('required|integer|min:1|max:50')]
    public $k = 5; // Giá trị mặc định
    public $metrics = [];
    public $evalError = '';

    // Quản lý huấn luyện
    public $progress = 0;
    public $trainingStatus = 'Chưa bắt đầu huấn luyện';
    public $trainingError = '';
    public $trainingSchedule = 'Đang tải lịch trình huấn luyện...';
    public $models = []; // Danh sách mô hình
    public $selectedModel = ''; // Mô hình được chọn

    // Thống kê tương tác
    public $totalUsersInteracted = 0;
    public $totalInteractions = 0;
    public $totalInteractionValue = 0;
    public $totalProductsInteracted = 0;
    public $interactionTypeDistribution = [];
    public $topInteractedProducts = [];

    // Thống kê gợi ý
    public $recommendationStats = [];
    public $recommendationsByDay = [];
    public $recommendationsByModel = [];
    public $recommendationConversionRate = [];

    // Thống kê tương tác
    public $interactionStats = [];

    // Thống kê tương tác theo thời gian
    public $interactionTimeline = [];

    public function mount()
    {
        $this->activeSection = 'statistic';
        $this->user_id = null;
        $this->method = 'hybrid';
        $this->n_items = 10;
        $this->alpha = 0.4;
        $this->beta = 0.3;
        $this->gamma = 0.15;
        $this->delta = 0.1;
        $this->epsilon = 0.05;
        $this->recommendations = [];
        $this->recError = null;
        $this->loadModels(); // Tải danh sách mô hình khi khởi tạo
        $this->loadStatisticsData();
    }

    public function setSection($section)
    {
        $this->activeSection = $section;

        if ($section == 'training') {
            $this->updateProgress();
            $this->loadModels(); // Cập nhật danh sách mô hình khi vào tab Training
        } elseif ($section == 'statistic') {
            $this->loadStatisticsData();
        }

        $this->dispatch('active-section-changed');
    }

    public function loadInteractionStats()
    {
        // Tổng số người dùng, tương tác, và sản phẩm có tương tác
        $this->totalUsersInteracted = Interaction::distinct('user_id')->count('user_id');
        $this->totalInteractions = Interaction::count();
        $this->totalInteractionValue = Interaction::sum('interaction_value') ?? 0;
        $this->totalProductsInteracted = Interaction::distinct('product_id')->count('product_id');

        // Thống kê số lượng theo loại tương tác
        $this->interactionTypeDistribution = Interaction::groupBy('interaction_type')
            ->selectRaw('interaction_type, COUNT(*) as count')
            ->get()
            ->pluck('count', 'interaction_type')
            ->toArray();

        // Thống kê chi tiết các loại tương tác
        $totalClicks = $this->interactionTypeDistribution['click'] ?? 0;
        $totalAddToCart = $this->interactionTypeDistribution['add_to_cart'] ?? 0;
        $totalOrders = $this->interactionTypeDistribution['order'] ?? 0;

        // Tính toán các chỉ số tỷ lệ
        $cvr = ($totalClicks > 0) ? ($totalOrders / $totalClicks) * 100 : 0; // CVR từ click đến order
        $avgClicksPerUser = $this->totalUsersInteracted > 0 ? ($totalClicks / $this->totalUsersInteracted) : 0;
        $cartPerClick = ($totalClicks > 0) ? ($totalAddToCart / $totalClicks) * 100 : 0; // Tỷ lệ thêm giỏ từ click
        $orderPerCart = ($totalAddToCart > 0) ? ($totalOrders / $totalAddToCart) * 100 : 0; // Tỷ lệ order từ add to cart
        $avgValuePerUser = $this->totalUsersInteracted > 0 ? ($this->totalInteractionValue / $this->totalUsersInteracted) : 0;

        // Thống kê giá trị trung bình theo loại tương tác
        $interactionValueByType = Interaction::groupBy('interaction_type')
            ->selectRaw('interaction_type, AVG(interaction_value) as avg_value')
            ->get()
            ->pluck('avg_value', 'interaction_type')
            ->toArray();

        // Lưu các thống kê
        $this->interactionStats = [
            'total_clicks' => $totalClicks,
            'total_add_to_cart' => $totalAddToCart,
            'total_orders' => $totalOrders,
            'cvr' => $cvr,
            'avg_clicks_per_user' => $avgClicksPerUser,
            'cart_per_click' => $cartPerClick,
            'order_per_cart' => $orderPerCart,
            'avg_value_per_user' => $avgValuePerUser,
            'interacted_products_count' => Interaction::whereIn('interaction_type', ['click', 'add_to_cart', 'order'])
                ->distinct('product_id')->count(),
            'interacted_users_count' => Interaction::whereIn('interaction_type', ['click', 'add_to_cart', 'order'])
                ->distinct('user_id')->count(),
            'interaction_value_by_type' => $interactionValueByType
        ];

        $this->interactionTimeline = $this->getInteractionTimeline();

        Log::info('Interaction Stats:', [
            'totalUsersInteracted' => $this->totalUsersInteracted,
            'totalInteractions' => $this->totalInteractions,
            'interactionStats' => $this->interactionStats,
        ]);
    }

    public function getRecommendations()
    {
        $this->validate();
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/recommend';
        try {
            $response = Http::timeout(20)->post($apiUrl, [
                'user_id' => (int) $this->user_id,
                'n_items' => (int) $this->n_items,
                'method' => $this->method,
                'alpha' => $this->method === 'hybrid' ? floatval($this->alpha) : 0,
                'beta' => $this->method === 'hybrid' ? floatval($this->beta) : 0,
                'gamma' => $this->method === 'hybrid' ? floatval($this->gamma) : 0,
                'delta' => $this->method === 'hybrid' ? floatval($this->delta) : 0,
                'epsilon' => $this->method === 'hybrid' ? floatval($this->epsilon) : 0,
            ]);

            if ($response->successful()) {
                $this->recommendations = $response->json();
                $this->recError = empty($this->recommendations) ? 'Không có gợi ý nào được trả về.' : '';
                session()->flash(
                    empty($this->recommendations) ? 'error' : 'success',
                    empty($this->recommendations) ? $this->recError : 'Lấy gợi ý thành công!'
                );
            } else {
                $this->recError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->recError);
            }
        } catch (\Exception $e) {
            $this->recError = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->recError);
        }
    }

    public function loadProducts()
    {
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/products/list';
        try {
            $response = Http::timeout(10)->get($apiUrl);
            if ($response->successful()) {
                $this->products = $response->json();
                $this->productError = '';
            } else {
                $this->productError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->productError);
            }
        } catch (\Exception $e) {
            $this->productError = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->productError);
        }
    }

    public function updatedProductId($value)
    {
        if ($value) {
            $apiUrl = env('API_URL', 'http://localhost:8001') . "/products/{$value}";
            try {
                $response = Http::timeout(10)->get($apiUrl);
                if ($response->successful()) {
                    $this->productDetails = $response->json();
                    $this->productError = '';
                    session()->flash('success', 'Tải chi tiết sản phẩm thành công!');
                } else {
                    $this->productDetails = null;
                    $this->productError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                    session()->flash('error', $this->productError);
                }
            } catch (\Exception $e) {
                $this->productDetails = null;
                $this->productError = "Lỗi: {$e->getMessage()}";
                session()->flash('error', $this->productError);
            }
        } else {
            $this->productDetails = null;
        }
    }

    public function evaluateModel()
    {
        $this->validate(['k' => 'required|integer|min:1|max:50']);
        $apiUrl = env('API_URL', 'http://localhost:8001') . "/metrics/evaluate_model?k={$this->k}";

        try {
            Log::info("Gọi API đánh giá mô hình: {$apiUrl} với k={$this->k}");
            $response = Http::timeout(30)->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                if (!is_array($data) || empty($data)) {
                    $this->evalError = "Dữ liệu không hợp lệ hoặc không có dữ liệu trả về.";
                    session()->flash('error', $this->evalError);
                    $this->metrics = [];
                } else {
                    $this->metrics = array_values($data);
                    $this->evalError = '';

                    session(['metrics' => $this->metrics, 'k' => $this->k]);
                    $this->dispatch('metricsUpdated', k: (int)$this->k, metrics: $this->metrics);
                    session()->flash('success', "Đánh giá mô hình thành công với k={$this->k}!");
                }
            } else {
                $this->evalError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->evalError);
                $this->metrics = [];
            }
        } catch (\Exception $e) {
            Log::error("Exception khi đánh giá mô hình: {$e->getMessage()}");
            $this->evalError = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->evalError);
            $this->metrics = [];
        }
    }

    public function getMetricsFromSession()
    {
        return session('metrics', []);
    }

    public function updateMetrics()
    {
        $this->metrics = $this->getMetricsFromSession();
        $this->dispatch('metricsUpdated', ['k' => $this->k, 'metrics' => $this->metrics]);
    }

    public function triggerTraining()
    {
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/train';
        try {
            $response = Http::timeout(10)->post($apiUrl);
            if ($response->successful()) {
                $this->trainingStatus = 'Huấn luyện đã được kích hoạt.';
                $this->trainingError = '';
                session()->flash('success', $response->json()['message']);
            } else {
                $this->trainingError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->trainingError);
            }
        } catch (\Exception $e) {
            $this->trainingError = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->trainingError);
        }
    }

    public function clearCache()
    {
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/clear_cache';
        try {
            $response = Http::timeout(10)->post($apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                $clearedKeys = $data['message'] ? (int)preg_replace('/[^0-9]/', '', $data['message']) : 0;
                $this->trainingError = '';
                if ($clearedKeys > 0) {
                    flash()->success("Đã xóa thành công {$clearedKeys} key cache gợi ý.");
                } else {
                    session()->flash('info', "Không có key cache nào để xóa.");
                }
            } else {
                $this->trainingError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->trainingError);
            }
        } catch (\Exception $e) {
            $this->trainingError = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->trainingError);
        }
    }

    public function loadTrainingSchedule()
    {
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/training/schedule';
        try {
            $response = Http::timeout(10)->get($apiUrl);
            if ($response->successful()) {
                $schedule = $response->json();
                $nextRun = $schedule['next_run'] ?? 'N/A';
                $trigger = $schedule['trigger'] ?? 'N/A';
                $this->trainingSchedule = "Lịch trình huấn luyện: {$trigger}. Lần chạy tiếp theo: {$nextRun}";
            } else {
                $this->trainingSchedule = "Lỗi: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->trainingSchedule);
            }
        } catch (\Exception $e) {
            $this->trainingSchedule = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->trainingSchedule);
        }
    }

    public function updateProgress()
    {
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/training/status';
        try {
            $response = Http::timeout(5)->get($apiUrl);
            if ($response->successful()) {
                $progressText = $response->body();
                $this->progress = floatval(str_replace('%', '', $progressText));
                $this->trainingStatus = $this->progress >= 100 ? 'Huấn luyện hoàn tất!' : "Đang huấn luyện: {$progressText}";
                $this->trainingError = '';
                if ($this->progress >= 100) {
                    $this->dispatch('stopPolling');
                }
            } else {
                $this->trainingError = "Lỗi API: " . ($response->json()['detail'] ?? $response->body());
                session()->flash('error', $this->trainingError);
            }
        } catch (\Exception $e) {
            $this->trainingError = "Lỗi: {$e->getMessage()}";
            session()->flash('error', $this->trainingError);
        }
    }

    public function loadRecommendationStats()
    {
        $this->recommendationStats = [
            'total_recommendations' => Interaction::where('interaction_type', 'view')->count(),
            'clicked_recommendations' => Interaction::where('interaction_type', 'click')->count(),
            'converted_recommendations' => Interaction::where('interaction_type', 'order')->count(),
            'avg_recommendation_value' => OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('user_interaction', function ($join) {
                    $join->on('user_interaction.product_id', '=', 'order_items.product_id')
                        ->where('user_interaction.interaction_type', 'order');
                })
                ->where('orders.status', 'delivered')
                ->avg(DB::raw('order_items.price * order_items.quantity')) ?? 0
        ];

        $cvr = ($this->recommendationStats['clicked_recommendations'] > 0)
            ? ($this->recommendationStats['converted_recommendations'] / $this->recommendationStats['clicked_recommendations']) * 100
            : 0;
        $this->recommendationStats['cvr'] = $cvr; // Thêm CVR vào recommendationStats

        $startDate = now()->subDays(6)->startOfDay();
        $this->recommendationsByDay = Interaction::selectRaw('DATE(created_at) as date, interaction_type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->whereIn('interaction_type', ['view', 'click', 'order'])
            ->groupBy('date', 'interaction_type')
            ->get()
            ->groupBy('date')
            ->map(function ($group) {
                $result = [
                    'date' => $group[0]->date,
                    'shown' => 0,
                    'clicked' => 0,
                    'converted' => 0
                ];
                foreach ($group as $item) {
                    if ($item->interaction_type === 'view') {
                        $result['shown'] = $item->count;
                    } elseif ($item->interaction_type === 'click') {
                        $result['clicked'] = $item->count;
                    } elseif ($item->interaction_type === 'order') {
                        $result['converted'] = $item->count;
                    }
                }
                return $result;
            })
            ->values()
            ->toArray();

        $this->recommendationsByModel = [];

        $this->recommendationConversionRate = Category::select('categories.id', 'categories.name')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->distinct()
            ->get()
            ->map(function ($category) {
                $clicked = Interaction::where('interaction_type', 'click')
                    ->join('products', 'user_interaction.product_id', '=', 'products.id')
                    ->where('products.category_id', $category->id)
                    ->count();
                $converted = Interaction::where('interaction_type', 'order')
                    ->join('products', 'user_interaction.product_id', '=', 'products.id')
                    ->where('products.category_id', $category->id)
                    ->count();
                $conversion_rate = $clicked > 0 ? round($converted / $clicked * 100, 2) : 0;

                return [
                    'category' => $category->name,
                    'conversion_rate' => $conversion_rate
                ];
            })
            ->toArray();

        Log::info('Recommendation Stats:', [
            'recommendationStats' => $this->recommendationStats,
            'recommendationsByDay' => $this->recommendationsByDay,
            'recommendationsByModel' => $this->recommendationsByModel,
            'recommendationConversionRate' => $this->recommendationConversionRate
        ]);
    }

    public function updateChartWithK()
    {
        Log::info("Updating chart with k={$this->k}");
        $this->dispatch('metricsUpdated', k: (int)$this->k, metrics: $this->metrics);
    }

    public function updatedK()
    {
        if (!empty($this->metrics)) {
            $this->updateChartWithK();
        }
    }

    private function getInteractionTimeline()
    {
        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay();

        $timeline = Interaction::selectRaw('DATE(created_at) as date, interaction_type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'interaction_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(function ($group) {
                $result = ['date' => $group[0]->date];
                foreach ($group as $item) {
                    $result[$item->interaction_type] = $item->count;
                }
                return $result;
            })
            ->values()
            ->toArray();

        return $timeline;
    }

    public function loadModels()
    {
        $apiUrl = env('API_URL', 'http://localhost:8001') . '/training/model_list'; // Sửa lại endpoint
        try {
            $response = Http::timeout(10)->get($apiUrl);
            if ($response->successful()) {
                $this->models = $response->json()['models'] ?? [];
            } else {
                $this->models = [];
                Log::warning("Không thể tải danh sách mô hình: " . ($response->json()['detail'] ?? $response->body()));
            }
        } catch (\Exception $e) {
            $this->models = [];
            Log::error("Lỗi khi tải danh sách mô hình: {$e->getMessage()}");
        }
    }

    public function selectModel()
    {
        if (!$this->selectedModel) {
            session()->flash('error', 'Vui lòng chọn một mô hình.');
            return;
        }

        $apiUrl = env('API_URL', 'http://localhost:8001') . '/select_model';
        try {
            $response = Http::timeout(10)->post($apiUrl, ['model_path' => $this->selectedModel]);
            if ($response->successful()) {
                session()->flash('success', $response->json()['message']);
            } else {
                session()->flash('error', "Lỗi API: " . ($response->json()['detail'] ?? $response->body()));
            }
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi: {$e->getMessage()}");
        }
    }

    public function render()
    {
        return view('livewire.admin.manage-recommendations-component');
    }

    private function loadStatisticsData()
    {
        $this->loadInteractionStats();
        $this->loadRecommendationStats();
        $this->interactionTimeline = $this->getInteractionTimeline();

        Log::info('Statistics Data Loaded', [
            'totalUsersInteracted' => $this->totalUsersInteracted,
            'totalInteractions' => $this->totalInteractions,
            'recommendationStats' => $this->recommendationStats,
            'interactionStats' => $this->interactionStats
        ]);
    }
}
