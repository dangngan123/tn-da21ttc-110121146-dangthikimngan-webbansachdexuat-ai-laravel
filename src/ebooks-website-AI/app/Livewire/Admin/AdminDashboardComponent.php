<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use App\Models\Category;
use App\Models\Product; // Thêm import để truy cập bảng products
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class AdminDashboardComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $pagesize = 5;
    public $timeFilter = 'today';

    public function updatedTimeFilter($value)
    {
        $this->resetPage();
    }

    public function refreshData()
    {
        Cache::forget("today_revenue_{$this->timeFilter}");
        Cache::forget('ordered_count');
        Cache::forget('processing_count');
        Cache::forget('shipped_count');
        Cache::forget('delivered_count');
        Cache::forget('canceled_count');
        Cache::forget('monthly_revenue');
        Cache::forget('monthly_orders');
        Cache::forget('last_month_revenue');
        Cache::forget('current_week_revenue');
        Cache::forget('last_week_revenue');
        Cache::forget('user_ratings');
        Cache::forget('orders_per_day');
        Cache::forget('monthly_revenue_data');
        Cache::forget('recent_orders');
        Cache::forget('top_products');
        Cache::forget('total_stock');
        Cache::forget('new_products');
        Cache::forget('new_customers_today');
        Cache::forget('new_customers_this_month');
        Cache::forget('total_customers');
        Cache::forget('top_customers');
        Cache::forget('repeat_customers');
        Cache::forget('new_customers_per_month');
        Cache::forget('category-revenue');

        flash()->success('Dữ liệu đã được làm mới thành công!');

        $this->dispatch('refreshCharts');
        $this->forceRender();
    }

    public function forceRender()
    {
        $this->timeFilter = $this->timeFilter;
    }

    public function render()
    {
        $dateQuery = match ($this->timeFilter) {
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            default => Carbon::today(),
        };

        // Doanh thu hôm nay (chỉ tính đơn hàng từ khách hàng không phải admin)
        $todayRevenue = Cache::remember("today_revenue_{$this->timeFilter}", 3600, function () use ($dateQuery) {
            $query = Order::where('status', 'delivered')
                ->whereHas('user', function ($query) {
                    $query->where('utype', '!=', 'ADM'); // Loại bỏ admin
                });
            return is_array($dateQuery)
                ? $query->whereBetween('created_at', $dateQuery)->sum('total') ?? 0
                : $query->whereDate('created_at', $dateQuery)->sum('total') ?? 0;
        });

        // Đếm đơn hàng theo trạng thái (loại bỏ đơn hàng từ admin)
        $orderedCount = Cache::remember('ordered_count', 3600, fn() => Order::where('status', 'ordered')
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->count() ?? 0);
        $processingCount = Cache::remember('processing_count', 3600, fn() => Order::where('status', 'processing')
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->count() ?? 0);
        $shippedCount = Cache::remember('shipped_count', 3600, fn() => Order::where('status', 'shipped')
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->count() ?? 0);
        $deliveredCount = Cache::remember('delivered_count', 3600, fn() => Order::where('status', 'delivered')
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->count() ?? 0);
        $canceledCount = Cache::remember('canceled_count', 3600, fn() => Order::where('status', 'canceled')
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->count() ?? 0);

        $totalOrders = $orderedCount + $processingCount + $shippedCount + $deliveredCount + $canceledCount;

        // Doanh thu tháng (loại bỏ admin)
        $monthlyRevenue = Cache::remember('monthly_revenue', 3600, fn() => Order::where('status', 'delivered')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->sum('total') ?? 0);

        // Đơn hàng tháng (loại bỏ admin)
        $monthlyOrders = Cache::remember('monthly_orders', 3600, fn() => Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->count() ?? 0);

        // Doanh thu tháng trước (loại bỏ admin)
        $lastMonthRevenue = Cache::remember('last_month_revenue', 3600, fn() => Order::where('status', 'delivered')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->sum('total') ?? 0);

        $monthGrowth = $lastMonthRevenue > 0 ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100) : 0;

        // Doanh thu tuần hiện tại (loại bỏ admin)
        $currentWeekRevenue = Cache::remember('current_week_revenue', 3600, fn() => Order::where('status', 'delivered')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()])
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->sum('total') ?? 0);

        // Doanh thu tuần trước (loại bỏ admin)
        $lastWeekRevenue = Cache::remember('last_week_revenue', 3600, fn() => Order::where('status', 'delivered')
            ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->sum('total') ?? 0);

        $weekGrowth = $lastWeekRevenue > 0 ? (($currentWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue * 100) : 0;

        // Đánh giá người dùng (giữ nguyên, vì không liên quan đến admin)
        $userRatings = Cache::remember('user_ratings', 3600, fn() => [
            1 => Review::where('rating', 1)->count() ?? 5,
            2 => Review::where('rating', 2)->count() ?? 8,
            3 => Review::where('rating', 3)->count() ?? 14,
            4 => Review::where('rating', 4)->count() ?? 33,
            5 => Review::where('rating', 5)->count() ?? 40,
        ]);

        $totalReviews = array_sum($userRatings);
        $positiveReviews = Review::whereIn('rating', [4, 5])->count() ?? 0;
        $positiveReviewRatio = $totalReviews > 0 ? ($positiveReviews / $totalReviews * 100) : 0;

        // Đơn hàng theo ngày (loại bỏ admin)
        $ordersPerDay = Cache::remember('orders_per_day', 3600, function () {
            $ordersPerDay = [];
            $startOfWeek = Carbon::today()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $currentDate = $startOfWeek->copy()->addDays($i);
                $ordersPerDay[] = [
                    'date' => $currentDate->format('l'),
                    'count' => Order::whereDate('created_at', $currentDate)
                        ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
                        ->count() ?? 0
                ];
            }
            return $ordersPerDay;
        });

        // Doanh thu theo tháng (loại bỏ admin)
        $monthlyRevenueData = Cache::remember('monthly_revenue_data', 3600, function () {
            $currentYear = Carbon::now()->year;
            $revenueData = [];
            for ($month = 1; $month <= 12; $month++) {
                $revenue = Order::where('status', 'delivered')
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $month)
                    ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
                    ->sum('total') ?? 0;
                $revenueData["T$month"] = $revenue;
            }
            return $revenueData;
        });

        // Doanh thu theo danh mục sản phẩm (loại bỏ admin)
        $categoryRevenue = Cache::remember('category-revenue', 3600, function () {
            return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.status', 'delivered')
                ->whereHas('order.user', fn($query) => $query->where('utype', '!=', 'ADM'))
                ->groupBy('categories.id', 'categories.name')
                ->selectRaw('categories.name, SUM(order_items.price * order_items.quantity) as revenue')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'revenue' => (float)$item->revenue
                    ];
                })
                ->toArray();
        });

        // Đơn hàng gần đây (loại bỏ admin)
        $orders = Cache::remember('recent_orders', 3600, fn() => Order::whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get());

        // Top 5 sản phẩm bán chạy nhất (dựa trên quantity từ OrderItem)
        $topProducts = Cache::remember('top_products', 3600, fn() => OrderItem::select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('SUM(quantity) DESC')
            ->take(5)
            ->with('product')
            ->get()
            ->map(fn($item) => [
                'name' => $item->product->name ?? 'N/A',
                'total_sold' => OrderItem::where('product_id', $item->product_id)
                    ->whereHas('order', fn($query) => $query->where('status', 'delivered')
                        ->whereHas('user', fn($q) => $q->where('utype', '!=', 'ADM')))
                    ->sum('quantity') ?? 0,
            ])
            ->toArray());

        // Tổng số lượng tồn kho (dựa trên quantity từ bảng products)
        $totalStock = Cache::remember('total_stock', 3600, fn() => Product::sum('quantity') ?? 0);

        // Sản phẩm mới (sản phẩm được thêm trong 30 ngày qua)
        $newProducts = Cache::remember('new_products', 3600, fn() => Product::where('created_at', '>=', Carbon::now()->subMonth())
            ->count() ?? 0);

        // Khách hàng mới hôm nay (loại bỏ admin)
        $newCustomersToday = Cache::remember('new_customers_today', 3600, fn() => User::whereDate('created_at', Carbon::today())
            ->where('utype', '!=', 'ADM')
            ->count() ?? 0);

        // Khách hàng mới trong tháng (loại bỏ admin)
        $newCustomersThisMonth = Cache::remember('new_customers_this_month', 3600, fn() => User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('utype', '!=', 'ADM')
            ->count() ?? 0);

        // Tổng số khách hàng (loại bỏ admin)
        $totalCustomers = Cache::remember('total_customers', 3600, fn() => User::where('utype', '!=', 'admin')->count() ?? 1);

        // Top 5 khách hàng hoạt động nhiều nhất (loại bỏ admin)
        $topCustomers = Cache::remember('top_customers', 3600, fn() => Order::select('user_id')
            ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
            ->groupBy('user_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->with('user')
            ->get()
            ->map(fn($order) => [
                'name' => $order->user->name ?? 'N/A',
                'email' => $order->user->email ?? 'N/A',
                'order_count' => Order::where('user_id', $order->user_id)
                    ->whereHas('user', fn($query) => $query->where('utype', '!=', 'ADM'))
                    ->count() ?? 0,
            ])
            ->toArray());

        // Khách hàng quay lại (loại bỏ admin)
        $repeatCustomers = Cache::remember('repeat_customers', 3600, fn() => User::where('utype', '!=', 'admin')
            ->whereHas('orders', fn($query) => $query->havingRaw('COUNT(*) > 1'))
            ->count() ?? 0);

        // Khách hàng mới theo tháng (loại bỏ admin)
        $newCustomersPerMonth = Cache::remember('new_customers_month', 3600, function () {
            $currentYear = Carbon::now()->year;
            $customersData = [];
            for ($month = 1; $month <= 12; $month++) {
                $customers = User::whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $month)
                    ->where('utype', '!=', 'ADM')
                    ->count() ?? 0;
                $customersData["T$month"] = $customers;
            }
            return $customersData;
        });

        $averageOrderValue = ($monthlyOrders > 0) ? ($monthlyRevenue / $monthlyOrders) : 0;

        return view('livewire.admin.admin-dashboard-component', [
            'orders' => $orders,
            'todayRevenue' => $todayRevenue,
            'totalOrders' => $totalOrders,
            'processingCount' => $processingCount,
            'shippedCount' => $shippedCount,
            'deliveredCount' => $deliveredCount,
            'canceledCount' => $canceledCount,
            'monthlyRevenue' => $monthlyRevenue,
            'monthGrowth' => $monthGrowth,
            'weekGrowth' => $weekGrowth,
            'userRatings' => $userRatings,
            'ordersPerDay' => $ordersPerDay,
            'topProducts' => $topProducts,
            'totalStock' => $totalStock,
            'newProducts' => $newProducts,
            'newCustomersToday' => $newCustomersToday,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'totalCustomers' => $totalCustomers,
            'topCustomers' => $topCustomers,
            'repeatCustomers' => $repeatCustomers,
            'newCustomersPerMonth' => $newCustomersPerMonth,
            'monthlyRevenueData' => $monthlyRevenueData,
            'averageOrderValue' => $averageOrderValue,
            'positiveReviewRatio' => $positiveReviewRatio,
            'timeFilter' => $this->timeFilter,
            'categoryRevenue' => $categoryRevenue,
        ]);
    }
}
