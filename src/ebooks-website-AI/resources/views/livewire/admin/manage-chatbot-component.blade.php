<div class="container-fluid">
    <!-- Hàng 1: Thống kê chính -->
    <div class="row g-4 mb-4">
        <!-- Tổng số người dùng chatbot -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm h-100" style="background: linear-gradient(135deg, #fef3c7, #fef9c3); transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Tổng số người dùng</h6>
                        <h4 class="card-title text-primary">{{ $stats['total_chatbot_users'] }}</h4>
                    </div>
                    <i class="fas fa-user-friends fa-2x ms-auto" style="color: #f59e0b;"></i>
                </div>
                <div class="card-footer text-muted" style="background: transparent;">
                    <i class="fas fa-clock" style="color: #f59e0b;"></i> Cập nhật: {{ now()->format('H:i') }}
                </div>
            </div>
        </div>

        <!-- Tương tác trong 1 giờ qua -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm h-100" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Tương tác 1 giờ qua</h6>
                        <h4 class="card-title text-primary">{{ $stats['interactions_last_hour'] }}</h4>
                    </div>
                    <i class="fas fa-stopwatch fa-2x ms-auto" style="color: #10b981;"></i>
                </div>
                <div class="card-footer text-muted" style="background: transparent;">
                    <i class="fas fa-clock" style="color: #10b981;"></i> Cập nhật: {{ now()->format('H:i') }}
                </div>
            </div>
        </div>

        <!-- Người dùng hoạt động (5 phút) -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm h-100" style="background: linear-gradient(135deg, #bfdbfe, #dbeafe); transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Người dùng hoạt động</h6>
                        <h4 class="card-title text-primary">{{ $stats['active_users_now'] }}</h4>
                    </div>
                    <i class="fas fa-user-check fa-2x ms-auto" style="color: #3b82f6;"></i>
                </div>
                <div class="card-footer text-muted" style="background: transparent;">
                    <i class="fas fa-clock" style="color: #3b82f6;"></i> Cập nhật: {{ now()->format('H:i') }}
                </div>
            </div>
        </div>

        <!-- Tương tác trung bình mỗi người dùng -->
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm h-100" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Tương tác trung bình</h6>
                        <h4 class="card-title text-primary">{{ $stats['avg_interactions_per_user'] }}</h4>
                    </div>
                    <i class="fas fa-chart-line fa-2x ms-auto" style="color: #ec4899;"></i>
                </div>
                <div class="card-footer text-muted" style="background: transparent;">
                    <i class="fas fa-clock" style="color: #ec4899;"></i> Cập nhật: {{ now()->format('H:i') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng 2: Biểu đồ -->
    <div class="row g-4">
        <!-- Biểu đồ cột: Tương tác theo loại dịch vụ -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">Tương tác theo loại dịch vụ</div>
                <div class="card-body">
                    <canvas id="supportOptionsChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ tròn: Tỷ lệ tương tác khách vs người dùng -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">Tỷ lệ tương tác</div>
                <div class="card-body">
                    <canvas id="interactionRatioChart" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ đường: Xu hướng tương tác 24h -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">Xu hướng tương tác 24 giờ qua</div>
                <div class="card-body">
                    <canvas id="interactionTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://rsms.me/inter/inter.css" rel="stylesheet">
<style>
.card:hover {
    transform: translateY(-5px);
}
.card-title {
    color: #1e40af !important;
}
.card-subtitle {
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Đợi DOM được tải hoàn toàn
document.addEventListener('DOMContentLoaded', function() {
    // Cấu hình chung cho tất cả biểu đồ
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#4b5563';

    // Palette màu tươi sáng
    const brightColors = [
        'rgba(245, 158, 11, 0.8)',  // Amber
        'rgba(16, 185, 129, 0.8)', // Emerald
        'rgba(236, 72, 153, 0.8)', // Pink
        'rgba(59, 130, 246, 0.8)', // Blue
        'rgba(139, 92, 246, 0.8)'  // Purple
    ];
    const brightBorderColors = [
        'rgb(245, 158, 11)',
        'rgb(16, 185, 129)',
        'rgb(236, 72, 153)',
        'rgb(59, 130, 246)',
        'rgb(139, 92, 246)'
    ];

    // Biểu đồ cột: Tương tác theo loại dịch vụ
    new Chart(document.getElementById('supportOptionsChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($stats['support_options'])),
            datasets: [{
                label: 'Số lượng tương tác',
                data: @json(array_values($stats['support_options'])),
                backgroundColor: brightColors,
                borderColor: brightBorderColors,
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutBounce'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượng tương tác',
                        color: '#1e40af'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Loại dịch vụ',
                        color: '#1e40af'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 64, 175, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} lượt`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Tương tác theo loại dịch vụ',
                    padding: {
                        bottom: 20
                    },
                    font: {
                        size: 16,
                        weight: '600'
                    },
                    color: '#1e40af'
                }
            }
        }
    });

    // Biểu đồ tròn: Tỷ lệ tương tác khách vs người dùng
    new Chart(document.getElementById('interactionRatioChart'), {
        type: 'pie',
        data: {
            labels: ['Khách', 'Người dùng đăng nhập'],
            datasets: [{
                data: [@json($stats['guest_interactions']), @json($stats['user_interactions'])],
                backgroundColor: [
                    'rgba(236, 72, 153, 0.8)', // Pink
                    'rgba(59, 130, 246, 0.8)'  // Blue
                ],
                borderColor: ['#ffffff', '#ffffff'],
                borderWidth: 2,
                hoverOffset: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutBounce'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        boxWidth: 12,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 64, 175, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / sum) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Tỷ lệ tương tác khách vs người dùng',
                    padding: {
                        bottom: 20
                    },
                    font: {
                        size: 16,
                        weight: '600'
                    },
                    color: '#1e40af'
                }
            }
        }
    });

    // Biểu đồ đường: Xu hướng tương tác 24h
    new Chart(document.getElementById('interactionTrendChart'), {
        type: 'line',
        data: {
            labels: @json(array_keys($stats['interaction_trend'])),
            datasets: [{
                label: 'Tương tác',
                data: @json(array_values($stats['interaction_trend'])),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutBounce'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượng tương tác',
                        color: '#1e40af'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Thời gian (giờ)',
                        color: '#1e40af'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 64, 175, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y} lượt`;
                        },
                        title: function(context) {
                            return `Giờ ${context[0].label}`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Xu hướng tương tác 24 giờ qua',
                    padding: {
                        bottom: 20
                    },
                    font: {
                        size: 16,
                        weight: '600'
                    },
                    color: '#1e40af'
                }
            }
        }
    });
});
</script>
@endpush