<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ManageStatisticsComponent extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = 'month';
    public $compareWithPrevious = true;
    public $selectedMetric = 'revenue';
    public $chartType = 'line';

    protected $queryString = ['filterType', 'selectedMetric', 'chartType'];
    protected $listeners = ['dateRangeUpdated' => 'updateDateRange', 'refreshStats' => '$refresh'];

    public function mount()
    {
        $this->initializeDateRange();
    }

    private function initializeDateRange()
    {
        switch ($this->filterType) {
            case 'today':
                $this->startDate = now()->format('Y-m-d');
                $this->endDate = now()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = now()->subDay()->format('Y-m-d');
                $this->endDate = now()->subDay()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $this->startDate = now()->subWeek()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'quarter':
                $this->startDate = now()->startOfQuarter()->format('Y-m-d');
                $this->endDate = now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = now()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
            default:
                if (!$this->startDate) $this->startDate = now()->subMonth()->format('Y-m-d');
                if (!$this->endDate) $this->endDate = now()->format('Y-m-d');
                break;
        }
    }

    public function updatedFilterType()
    {
        $this->initializeDateRange();
    }

    public function updateDateRange($startDate, $endDate)
    {
        $this->filterType = 'custom';
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getPreviousPeriodData()
    {
        // Tính toán khoảng thời gian trước đó có cùng độ dài
        $currentStart = Carbon::parse($this->startDate);
        $currentEnd = Carbon::parse($this->endDate);
        $daysDiff = $currentStart->diffInDays($currentEnd) + 1;

        $previousStart = (clone $currentStart)->subDays($daysDiff);
        $previousEnd = (clone $previousStart)->addDays($daysDiff - 1);

        $previousOrders = Order::whereBetween('created_at', [
            $previousStart->format('Y-m-d') . ' 00:00:00',
            $previousEnd->format('Y-m-d') . ' 23:59:59'
        ])->get();

        return [
            'revenue' => $previousOrders->sum('total'),
            'orders_count' => $previousOrders->count(),
            'period' => [
                'start' => $previousStart->format('Y-m-d'),
                'end' => $previousEnd->format('Y-m-d')
            ]
        ];
    }

    public function render()
    {
        // Sử dụng cache để tối ưu hiệu suất
        $cacheKey = "stats_{$this->filterType}_{$this->startDate}_{$this->endDate}";

        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $orders = Order::whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])->get();

            $products = Product::all();
            $users = User::where('utype', 'USR')->count();

            // Thống kê cơ bản
            $statistics = [
                'revenue' => $orders->sum('total'),
                'orders_count' => $orders->count(),
                'products_count' => $products->count(),
                'low_stock_count' => $products->where('quantity', '<', 10)->count(),
                'users_count' => $users,
                'avg_order_value' => $orders->count() > 0 ? $orders->sum('total') / $orders->count() : 0
            ];

            // Dữ liệu biểu đồ doanh thu
            $revenueData = $this->getChartData('revenue');

            // Dữ liệu biểu đồ danh mục sản phẩm
            $categoryData = Product::select('category_id', DB::raw('count(*) as count'))
                ->groupBy('category_id')
                ->with('category')
                ->get()
                ->map(function ($item) {
                    return [
                        'count' => $item->count,
                        'category' => [
                            'name' => $item->category->name ?? 'Chưa phân loại'
                        ]
                    ];
                });

            // Dữ liệu biểu đồ đơn hàng
            $orderCountData = $this->getChartData('orders');

            // Dữ liệu trạng thái đơn hàng
            $orderStatusData = Order::whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            return [
                'statistics' => $statistics,
                'revenueData' => $revenueData,
                'categoryData' => $categoryData,
                'orderCountData' => $orderCountData,
                'orderStatusData' => $orderStatusData
            ];
        });

        // Lấy dữ liệu kỳ trước để so sánh
        $previousPeriodData = $this->compareWithPrevious ? $this->getPreviousPeriodData() : null;

        return view('livewire.admin.manage-statistics-component', array_merge($data, [
            'previousPeriodData' => $previousPeriodData
        ]));
    }

    private function getChartData($type)
    {
        $dateFormat = $this->getDateFormatForGrouping();

        if ($type === 'revenue') {
            return Order::select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('SUM(total) as value')
            )
                ->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ])
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$dateFormat}')"))
                ->orderBy('date')
                ->get();
        } else {
            return Order::select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('count(*) as value')
            )
                ->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ])
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$dateFormat}')"))
                ->orderBy('date')
                ->get();
        }
    }

    private function getDateFormatForGrouping()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $diffInDays = $start->diffInDays($end);

        if ($diffInDays <= 1) {
            return '%H:00'; // Theo giờ
        } elseif ($diffInDays <= 31) {
            return '%Y-%m-%d'; // Theo ngày
        } elseif ($diffInDays <= 90) {
            return '%Y-%m-%d'; // Vẫn theo ngày cho quý
        } else {
            return '%Y-%m'; // Theo tháng cho năm
        }
    }

    public function applyCustomDateFilter()
    {
        // Kiểm tra và xác thực ngày
        if (empty($this->startDate) || empty($this->endDate)) {
            session()->flash('error', 'Vui lòng chọn cả ngày bắt đầu và ngày kết thúc');
            return;
        }
        
        if (strtotime($this->endDate) < strtotime($this->startDate)) {
            session()->flash('error', 'Ngày kết thúc phải sau ngày bắt đầu');
            return;
        }
        
        // Cập nhật dữ liệu thống kê với khoảng thời gian tùy chỉnh
        $this->filterType = 'custom'; // Đảm bảo filterType được đặt thành 'custom'
        
        // Không cần gọi loadStatistics vì Livewire sẽ tự động render lại
        session()->flash('success', 'Đã áp dụng khoảng thời gian tùy chỉnh');
    }

    /**
     * Tính toán phần trăm thay đổi giữa hai giá trị
     * 
     * @param float $current Giá trị hiện tại
     * @param float $previous Giá trị trước đó
     * @return float Phần trăm thay đổi
     */
    public function calculatePercentChange($current, $previous)
    {
        if ($previous == 0 && $current > 0) {
            return 100; // Tăng 100% nếu giá trị trước là 0 và giá trị hiện tại > 0
        } elseif ($previous == 0 && $current == 0) {
            return 0; // Không thay đổi nếu cả hai giá trị đều là 0
        } elseif ($current == 0 && $previous > 0) {
            return -100; // Giảm 100% nếu giá trị hiện tại là 0 và giá trị trước > 0
        } else {
            return (($current - $previous) / $previous) * 100;
        }
    }
}



