<div>
    <style>
    .btn-light {
    background-color: #ffffff;
    border: 1px solid #ffffff;
    color: #1e2a44;
    font-weight: 500;
    padding: 6px 12px; /* Điều chỉnh padding nút */
    height: 34px; /* Chiều cao nút cố định */
    font-size: 14px; /* Kích thước chữ nút */
    border-radius: 5px; /* Bo góc nhẹ cho nút */
}

.btn-light:hover {
    background-color: #f8f9fa;
    color: #1e2a44;
    border-color: #f8f9fa;
}
</style>
    <div class="container-fluid">



     <div class="dashboard-header row align-items-center mb-4">
    <div class="col-md-8">
       <h2 class="h3 mb-0 text-gray-800" style="color:#ffffff; font-weight: bold;">Thống kê & Phân tích</h2>
       <p class="text-white mb-0">Thống kê và phân tích hiệu suất cửa hàng</p>
    </div>
    <div class="col-md-4 text-end">
        <button wire:click="$refresh" class=" btn btn-light btn-sm" style="color: #1e2a44; border-bottom: #ffffff;">
                    <i class="fas fa-sync-alt me-1"></i>  Làm mới dữ liệu
                </button>
       
    </div>

</div>

     
              

        <!-- Header Section -->
       

        <!-- Filter Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="form-label small text-muted">Khoảng thời gian</label>
                        <select wire:model.live="filterType" class="form-select form-select-sm">
                            <option value="today">Hôm nay</option>
                            <option value="yesterday">Hôm qua</option>
                            <option value="week">Tuần này</option>
                            <option value="last_week">Tuần trước</option>
                            <option value="month">Tháng này</option>
                            <option value="last_month">Tháng trước</option>
                            <option value="quarter">Quý này</option>
                            <option value="year">Năm nay</option>
                            <option value="custom">Tùy chỉnh</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="form-label small text-muted">Từ ngày</label>
                        <input type="date" wire:model="startDate" class="form-control form-control-sm" {{ $filterType !== 'custom' ? 'disabled' : '' }} />
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="form-label small text-muted">Đến ngày</label>
                        <input type="date" wire:model="endDate" class="form-control form-control-sm" {{ $filterType !== 'custom' ? 'disabled' : '' }} />
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0 d-flex flex-column">
                        <label class="form-label small text-muted">So sánh với kỳ trước</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" wire:model.live="compareWithPrevious" id="compareSwitch">
                                <label class="form-check-label" for="compareSwitch">{{ $compareWithPrevious ? 'Bật' : 'Tắt' }}</label>
                            </div>
                            @if($filterType === 'custom')
                            <button wire:click="applyCustomDateFilter" class="btn btn-sm btn-primary">
                                <i class="fas fa-check me-1"></i> Áp dụng
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4" >
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh Thu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['revenue'], 3, ',', '.') }} đ</div>
                                @if($compareWithPrevious && isset($previousPeriodData))
                                    @php
                                        $percentChange = $this->calculatePercentChange($statistics['revenue'], $previousPeriodData['revenue']);
                                    @endphp
                                    <div class="mt-2 small">
                                        @if($percentChange > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up me-1"></i>{{ number_format($percentChange, 1) }}%</span>
                                        @elseif($percentChange < 0)
                                            <span class="text-danger"><i class="fas fa-arrow-down me-1"></i>{{ number_format(abs($percentChange), 1) }}%</span>
                                        @else
                                            <span class="text-muted"><i class="fas fa-equals me-1"></i>0%</span>
                                        @endif
                                        <span class="text-muted ms-1">so với kỳ trước</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đơn Hàng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['orders_count'] }}</div>
                                @if($compareWithPrevious && isset($previousPeriodData))
                                    @php
                                        $percentChange = $this->calculatePercentChange($statistics['orders_count'], $previousPeriodData['orders_count']);
                                    @endphp
                                    <div class="mt-2 small">
                                        @if($percentChange > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up me-1"></i>{{ number_format($percentChange, 1) }}%</span>
                                        @elseif($percentChange < 0)
                                            <span class="text-danger"><i class="fas fa-arrow-down me-1"></i>{{ number_format(abs($percentChange), 1) }}%</span>
                                        @else
                                            <span class="text-muted"><i class="fas fa-equals me-1"></i>0%</span>
                                        @endif
                                        <span class="text-muted ms-1">so với kỳ trước</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Giá Trị Đơn TB</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['avg_order_value'], 3, ',', '.') }} đ</div>
                                <div class="mt-2 small">
                                    <span class="text-muted">{{ $statistics['orders_count'] }} đơn hàng</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sản Phẩm Sắp Hết</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['low_stock_count'] }}</div>
                                <div class="mt-2 small">
                                    <span class="text-muted">Trên tổng {{ $statistics['products_count'] }} sản phẩm</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <!-- Revenue Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="m-0 font-weight-bold text-primary">Doanh Thu</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn {{ $chartType == 'line' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('chartType', 'line')">
                                <i class="fas fa-chart-line"></i>
                            </button>
                            <button type="button" class="btn {{ $chartType == 'bar' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('chartType', 'bar')">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button type="button" class="btn {{ $chartType == 'area' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('chartType', 'area')">
                                <i class="fas fa-chart-area"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueChart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Category Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="m-0 font-weight-bold text-primary">Phân Bố Sản Phẩm</h5>
                    </div>
                    <div class="card-body">
                        <div id="categoryChart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row Charts -->
        <div class="row">
            <!-- Orders Chart -->
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="m-0 font-weight-bold text-primary">Số Lượng Đơn Hàng</h5>
                       
                    </div>
                    <div class="card-body">
                        <div id="ordersChart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Order Status Distribution -->
            
        </div>
    </div>
  @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeStatisticsCharts();
});

document.addEventListener('livewire:update', function() {
    initializeStatisticsCharts();
});

// Sử dụng tên biến cụ thể hơn để tránh xung đột
let statisticsCharts = {
    revenueChart: null,
    categoryChart: null,
    ordersChart: null,
    orderStatusChart: null
};

function initializeStatisticsCharts() {
    try {
        // Revenue Chart
        const revenueData = @json($revenueData);
        const chartType = @json($chartType);
        
        const revenueChartElement = document.querySelector("#revenueChart");
        if (revenueChartElement) {
            // Hủy biểu đồ cũ nếu tồn tại
            if (statisticsCharts.revenueChart) {
                statisticsCharts.revenueChart.destroy();
                statisticsCharts.revenueChart = null;
            }
            
            const revenueOptions = {
                series: [{
                    name: 'Doanh Thu',
                    data: revenueData.map(item => item.value)
                }],
                chart: {
                    type: chartType === 'area' ? 'area' : (chartType === 'bar' ? 'bar' : 'line'),
                    height: 350,
                    fontFamily: 'Nunito, sans-serif',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: { enabled: true, delay: 150 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: chartType === 'bar' ? 0 : 3 },
                colors: ['#009688'], // Xanh ngọc đậm
                fill: {
                    type: chartType === 'area' ? 'gradient' : 'solid',
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        shadeIntensity: 0.7,
                        gradientToColors: ['#00796B'], // Xanh lá đậm hơn
                        inverseColors: false,
                        opacityFrom: 0.9,
                        opacityTo: 0.5,
                        stops: [0, 100]
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 5,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: true } },
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                xaxis: {
                    categories: revenueData.map(item => item.date),
                    labels: {
                        style: { colors: '#858796', fontSize: '12px', fontFamily: 'Nunito, sans-serif', fontWeight: 400 }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                maximumFractionDigits: 0
                            }).format(value);
                        },
                        style: { colors: '#858796', fontSize: '12px', fontFamily: 'Nunito, sans-serif', fontWeight: 400 }
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    },
                    marker: { show: true }
                }
            };

            statisticsCharts.revenueChart = new ApexCharts(revenueChartElement, revenueOptions);
            statisticsCharts.revenueChart.render();
        }

        // Category Chart
        const categoryData = @json($categoryData);
        
        const categoryChartElement = document.querySelector("#categoryChart");
        if (categoryChartElement) {
            // Hủy biểu đồ cũ nếu tồn tại
            if (statisticsCharts.categoryChart) {
                statisticsCharts.categoryChart.destroy();
                statisticsCharts.categoryChart = null;
            }
            
            const categoryOptions = {
                series: categoryData.map(item => item.count),
                chart: {
                    type: 'donut',
                    height: 350,
                    fontFamily: 'Nunito, sans-serif',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: { enabled: true, delay: 150 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    }
                },
                labels: categoryData.map(item => item.category.name),
                colors: [
                    '#F4A261', '#DC3545', '#00695C', '#0288D1', '#388E3C',
                    '#F4511E', '#AB47BC', '#FFB300', '#455A64', '#C2185B',
                    '#00796B', '#EF6C00', '#2E7D32', '#8E24AA', '#00695C'
                ], // Màu đậm hơn
                plotOptions: {
                    pie: {
                        donut: {
                            size: '50%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '14px', fontFamily: 'Nunito, sans-serif', color: '#858796' },
                                value: { show: true, fontSize: '16px', fontFamily: 'Nunito, sans-serif', color: '#858796', formatter: val => val },
                                total: {
                                    show: true,
                                    label: 'Tổng',
                                    color: '#858796',
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                responsive: [{
                    breakpoint: 480,
                    options: { chart: { height: 300 }, legend: { position: 'bottom' } }
                }],
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    fontFamily: 'Nunito, sans-serif',
                    fontWeight: 400,
                    markers: { width: 12, height: 12, strokeWidth: 0, radius: 12 },
                    itemMargin: { horizontal: 5, vertical: 0 }
                },
                tooltip: {
                    enabled: true,
                    theme: 'light',
                    y: { formatter: value => value + ' sản phẩm' }
                }
            };

            statisticsCharts.categoryChart = new ApexCharts(categoryChartElement, categoryOptions);
            statisticsCharts.categoryChart.render();
        }

        // Orders Chart
        const orderData = @json($orderCountData);
        
        const ordersChartElement = document.querySelector("#ordersChart");
        if (ordersChartElement) {
            // Hủy biểu đồ cũ nếu tồn tại
            if (statisticsCharts.ordersChart) {
                statisticsCharts.ordersChart.destroy();
                statisticsCharts.ordersChart = null;
            }
            
            const ordersOptions = {
                series: [{
                    name: 'Đơn Hàng',
                    data: orderData.map(item => item.value)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    fontFamily: 'Nunito, sans-serif',
                    toolbar: { show: true },
                    animations: { enabled: true, easing: 'easeinout', speed: 800 }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                colors: ['#E64A19'], // Cam đậm
                fill: {
                    opacity: 1,
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        shadeIntensity: 0.7,
                        gradientToColors: ['#BF360C'], // Cam nâu đậm
                        inverseColors: false,
                        opacityFrom: 0.9,
                        opacityTo: 0.5,
                        stops: [0, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 5,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: true } },
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                xaxis: {
                    categories: orderData.map(item => item.date),
                    labels: {
                        style: {
                            colors: '#858796',
                            fontSize: '12px',
                            fontFamily: 'Nunito, sans-serif',
                            fontWeight: 400
                        }
                    }
                },
                yaxis: {
                    title: { text: 'Số lượng đơn hàng', style: { color: '#858796', fontSize: '12px', fontFamily: 'Nunito, sans-serif', fontWeight: 600 } },
                    labels: { style: { colors: '#858796', fontSize: '12px', fontFamily: 'Nunito, sans-serif', fontWeight: 400 } }
                },
                tooltip: {
                    theme: 'light',
                    y: { formatter: value => value + ' đơn hàng' }
                }
            };

            statisticsCharts.ordersChart = new ApexCharts(ordersChartElement, ordersOptions);
            statisticsCharts.ordersChart.render();
        }

        // Order Status Chart
        const orderStatusData = @json($orderStatusData ?? []);
        const statusLabels = {
            'ordered': 'Đã đặt hàng',
            'delivered': 'Đã giao hàng',
            'canceled': 'Đã hủy',
            'processing': 'Đang xử lý',
            'pending': 'Chờ xác nhận'
        };
        
        const orderStatusChartElement = document.querySelector("#orderStatusChart");
        if (orderStatusChartElement) {
            // Hủy biểu đồ cũ nếu tồn tại
            if (statisticsCharts.orderStatusChart) {
                statisticsCharts.orderStatusChart.destroy();
                statisticsCharts.orderStatusChart = null;
            }
            
            const statusLabelsArray = [];
            const statusDataArray = [];
            
            Object.keys(orderStatusData).forEach(key => {
                statusLabelsArray.push(statusLabels[key] || key);
                statusDataArray.push(orderStatusData[key]);
            });
            
            const orderStatusOptions = {
                series: statusDataArray,
                chart: {
                    type: 'pie',
                    height: 350,
                    fontFamily: 'Nunito, sans-serif'
                },
                labels: statusLabelsArray,
                colors: ['#C62828', '#00695C', '#F9A825', '#0288D1', '#2E7D32'], // Màu đậm hơn
                responsive: [{
                    breakpoint: 480,
                    options: { chart: { height: 300 }, legend: { position: 'bottom' } }
                }],
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    fontFamily: 'Nunito, sans-serif',
                    fontWeight: 400
                },
                tooltip: {
                    enabled: true,
                    theme: 'light',
                    y: { formatter: value => value + ' đơn hàng' }
                }
            };

            statisticsCharts.orderStatusChart = new ApexCharts(orderStatusChartElement, orderStatusOptions);
            statisticsCharts.orderStatusChart.render();
        }
    } catch (error) {
        console.error("Error initializing statistics charts:", error);
    }
}
</script>
@endpush
</div>
