
<div>
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --background-light: #f9fafb;
            --background-dark: #2d3748;
            --text-light: #1f2937;
            --text-dark: #e5e7eb;
            --transition: all 0.3s ease;
            --shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            --border-radius: 8px;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --background-light: #2d3748;
                --background-dark: #1f2937;
                --text-light: #e5e7eb;
                --text-dark: #9ca3af;
            }

            .card { background: var(--background-dark); }
            .table { color: var(--text-light); }
            .form-control, .form-select {
                background: #374151;
                color: var(--text-light);
                border-color: #4b5563;
            }
            .form-control:focus, .form-select:focus {
                background: #374151;
                color: var(--text-light);
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            }
        }

        .card { border-radius: 8px; box-shadow: var(--shadow); }
        .table-responsive { margin-top: 16px; }
        .table th, .table td { vertical-align: middle; }
        .stat-card {
            padding: 1.25rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .stat-card h6 {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: rgba(0, 0, 0, 0.6);
        }
        .stat-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: rgba(0, 0, 0, 0.8);
        }
        .stat-card .rate {
            font-size: 0.875rem;
            color: rgba(0, 0, 0, 0.6);
        }
        .chart-container {
            position: relative;
            min-height: 300px;
        }
        .chart-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 10;
        }
        .chart-loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* CSS cho icon màu */
        .icon-terms { color: #FF6B6B; }
        .icon-lookup { color: #007BFF; }
        .icon-evaluation { color: #28A745; }
        .icon-training { color: #6F42C1; }
        .icon-statistic { color: #FFC107; }

        .nav-link:hover .icon-terms, .nav-link.active .icon-terms { color: #E55A5A; }
        .nav-link:hover .icon-lookup, .nav-link.active .icon-lookup { color: #0056B3; }
        .nav-link:hover .icon-evaluation, .nav-link.active .icon-evaluation { color: #1F7A33; }
        .nav-link:hover .icon-training, .nav-link.active .icon-training { color: #5A2D9C; }
        .nav-link:hover .icon-statistic, .nav-link.active .icon-statistic { color: #D39E00; }

        @media (max-width: 576px) {
            .nav-link i { margin-right: 0.5rem; font-size: 1rem; }
        }
    </style>

   

    <div class="dashboard-header row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="mb-0 text-white">Gợi Ý Sách</h1>
            <p class="text-white mb-0">Quản lý và theo dõi hiệu suất gợi ý sách </p>
        </div>
       
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="recommendationTabs" role="tablist" style="margin-left: 20px;">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeSection == 'terms' ? 'active' : '' }}"
                    wire:click="setSection('terms')"
                    id="terms-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#terms"
                    type="button"
                    role="tab">
                <i class="fas fa-book-open me-1 icon-terms"></i> Gợi Ý Sách
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeSection == 'lookup' ? 'active' : '' }}"
                    wire:click="setSection('lookup')"
                    id="lookup-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#lookup"
                    type="button"
                    role="tab">
                <i class="fas fa-search me-1 icon-lookup"></i> Tra Cứu Sách
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeSection == 'evaluation' ? 'active' : '' }}"
                    wire:click="setSection('evaluation')"
                    id="evaluation-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#evaluation"
                    type="button"
                    role="tab">
                <i class="fas fa-chart-bar me-1 icon-evaluation"></i> Đánh Giá Mô Hình
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeSection == 'training' ? 'active' : '' }}"
                    wire:click="setSection('training')"
                    id="training-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#training"
                    type="button"
                    role="tab">
                <i class="fas fa-brain me-1 icon-training"></i> Huấn Luyện Mô Hình
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeSection == 'statistic' ? 'active' : '' }}"
                    wire:click="setSection('statistic')"
                    id="statistic-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#statistic"
                    type="button"
                    role="tab">
                <i class="fas fa-chart-pie me-1 icon-statistic"></i> Thống Kê
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="recommendationTabsContent">
       <!-- Tab Gợi Ý Sách -->
<div class="tab-pane fade {{ $activeSection == 'terms' ? 'show active' : '' }}" id="terms" role="tabpanel" style="margin-left: 20px; margin-right: 20px">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Gợi Ý Sách Cho Người Dùng</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="number" class="form-control @error('user_id') is-invalid @enderror"
                           wire:model="user_id" id="user_id" min="1">
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="method" class="form-label">Phương Thức Gợi Ý</label>
                    <select class="form-select @error('method') is-invalid @enderror"
                            wire:model="method" id="method">
                        <option value="hybrid">Hybrid (SVD + Content)</option>
                        <option value="als_user">ALS User-based</option>
                        <option value="als_item">ALS Item-based</option>
                        <option value="user_based_svd">SVD User-based</option>
                        <option value="item_based_svd">SVD Item-based</option>
                        <option value="content_based">Content-based</option>
                    </select>
                    @error('method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="n_items" class="form-label">Số Lượng Gợi Ý</label>
                    <input type="number" class="form-control @error('n_items') is-invalid @enderror"
                           wire:model="n_items" id="n_items" min="1" max="50">
                    @error('n_items') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <!-- Thêm các trường nhập trọng số -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="alpha" class="form-label">Alpha (User-based SVD)</label>
                    <input type="number" class="form-control @error('alpha') is-invalid @enderror"
                           wire:model="alpha" id="alpha" min="0" max="1" step="0.05" placeholder="0.4">
                    @error('alpha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="beta" class="form-label">Beta (Item-based SVD)</label>
                    <input type="number" class="form-control @error('beta') is-invalid @enderror"
                           wire:model="beta" id="beta" min="0" max="1" step="0.05" placeholder="0.3">
                    @error('beta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="gamma" class="form-label">Gamma (Content-based)</label>
                    <input type="number" class="form-control @error('gamma') is-invalid @enderror"
                           wire:model="gamma" id="gamma" min="0" max="1" step="0.05" placeholder="0.15">
                    @error('gamma') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="delta" class="form-label">Delta (ALS User)</label>
                    <input type="number" class="form-control @error('delta') is-invalid @enderror"
                           wire:model="delta" id="delta" min="0" max="1" step="0.05" placeholder="0.1">
                    @error('delta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label for="epsilon" class="form-label">Epsilon (ALS Item)</label>
                    <input type="number" class="form-control @error('epsilon') is-invalid @enderror"
                           wire:model="epsilon" id="epsilon" min="0" max="1" step="0.05" placeholder="0.05">
                    @error('epsilon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tổng Trọng Số</label>
                    <input type="number" class="form-control" value="{{ number_format($alpha + $beta + $gamma + $delta + $epsilon, 2) }}" readonly>
                </div>
            </div>
            <button class="btn btn-primary mb-3" wire:click="getRecommendations" wire:loading.attr="disabled">
                <span wire:loading wire:target="getRecommendations">Đang tải...</span>
                <span wire:loading.remove wire:target="getRecommendations">Lấy Gợi Ý</span>
            </button>
            @if ($recError)
            <div class="text-danger mb-3">{{ $recError }}</div>
            @endif
            @if (!empty($recommendations))
            <h6>Kết Quả Gợi Ý ({{ count($recommendations) }} sách):</h6>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Sách</th>
                            <th>Điểm Dự Đoán</th>
                            <th>Tác Giả</th>
                            <th>Đã Bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recommendations as $rec)
                        <tr>
                            <td>{{ $rec['id'] }}</td>
                            <td>{{ $rec['name'] }}</td>
                            <td>{{ number_format($rec['score'], 4) }}</td>
                            <td>{{ $rec['author'] ?? 'N/A' }}</td>
                            <td>{{ $rec['sold_count'] ?? '0' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-danger">{{ $recError ?? 'Không có gợi ý nào cho người dùng này.' }}</p>
            @endif
        </div>
    </div>
</div>

        <!-- Tab Tra Cứu Sách -->
        <div class="tab-pane fade {{ $activeSection == 'lookup' ? 'show active' : '' }}" id="lookup" role="tabpanel" style="margin-left: 20px; margin-right: 20px">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tra Cứu Thông Tin Sách</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Chọn Sách</label>
                        <select class="form-select @error('product_id') is-invalid @enderror"
                            wire:model.live="product_id" id="product_id">
                            <option value="">Chọn một sách...</option>
                            @foreach ($products as $product)
                            <option value="{{ $product['id'] }}">{{ $product['name'] }} (ID: {{ $product['id'] }})</option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @if ($productError)
                    <div class="text-danger mb-3">{{ $productError }}</div>
                    @endif
                    @if ($productDetails)
                    <h6>Chi Tiết Sách: {{ $productDetails['name'] }}</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>ID:</strong> {{ $productDetails['id'] }}</p>
                            <p><strong>Tác Giả:</strong> {{ $productDetails['author'] ?? 'N/A' }}</p>
                            <p><strong>Nhà Xuất Bản:</strong> {{ $productDetails['publisher'] ?? 'N/A' }}</p>
                            <p><strong>Mô Tả Ngắn:</strong> {{ $productDetails['short_description'] ?? 'N/A' }}</p>
                            <p><strong>Giá Gốc:</strong> {{ $productDetails['reguler_price'] ? number_format($productDetails['reguler_price'], 0) . ' đ' : 'N/A' }}</p>
                            <p><strong>Giá Bán:</strong> {{ $productDetails['sale_price'] ? number_format($productDetails['sale_price'], 0) . ' đ' : 'N/A' }}</p>
                            <p><strong>Đã Bán:</strong> {{ $productDetails['sold_count'] ?? '0' }}</p>
                            <p><strong>Tồn Kho:</strong> {{ $productDetails['quantity'] ?? '0' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

<!-- Tab Đánh Giá Mô Hình -->
<div class="tab-pane fade {{ $activeSection == 'evaluation' ? 'show active' : '' }}" id="evaluation" role="tabpanel" style="margin-left: 20px; margin-right: 20px">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Đánh Giá Mô Hình Gợi Ý</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="number" wire:model.live="k" class="form-control" placeholder="Nhập giá trị k" min="1" max="50">
                        <button class="btn btn-primary" wire:click="evaluateModel" wire:loading.attr="disabled">
                            <span wire:loading wire:target="evaluateModel">Đang đánh giá...</span>
                            <span wire:loading.remove wire:target="evaluateModel">Đánh Giá Mô Hình</span>
                        </button>
                    </div>
                    @error('k') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            @if($evalError)
            <div class="alert alert-danger">{{ $evalError }}</div>
            @endif
            <!-- Biểu đồ đánh giá -->
            <div class="row mb-4" wire:ignore>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0" id="modelRadarChart-title">So Sánh Tổng Quan Các Mô Hình @k={{ $k }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="modelRadarChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0" id="precisionRecallChart-title">Precision & Recall @k={{ $k }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="precisionRecallChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0" id="ndcgChart-title">NDCG @k={{ $k }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="ndcgChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0" id="diversityCoverageChart-title">Diversity & Coverage @k={{ $k }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="diversityCoverageChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive" id="metrics-table">
                @if(!empty($metrics))
                <table class="table table-striped">
                    <thead>
                        <tr id="metrics-table-header">
                            <th>Mô Hình</th>
                            <th>Precision @k={{ $k }}</th>
                            <th>Recall @k={{ $k }}</th>
                            <th>NDCG @k={{ $k }}</th>
                            <th>Diversity @k={{ $k }}</th>
                            <th>Coverage @k={{ $k }}</th>
                        </tr>
                    </thead>
                    <tbody id="metrics-table-body">
                        @foreach($metrics as $metric)
                        <tr>
                            <td>{{ $metric['method'] }}</td>
                            <td>{{ number_format($metric['precision_at_k'], 4) }}</td>
                            <td>{{ number_format($metric['recall_at_k'], 4) }}</td>
                            <td>{{ number_format($metric['ndcg_at_k'], 4) }}</td>
                            <td>{{ $metric['diversity_at_k'] !== null ? number_format($metric['diversity_at_k'], 4) : 'N/A' }}</td>
                            <td>{{ number_format($metric['coverage_at_k'], 4) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-danger">Không có dữ liệu để hiển thị.</p>
                @endif
            </div>
        </div>
    </div>
</div>

       <!-- Tab Quản Lý Huấn Luyện -->
<div class="tab-pane fade {{ $activeSection == 'training' ? 'show active' : '' }}" id="training" role="tabpanel" style="margin-left: 20px; margin-right: 20px">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Quản Lý Huấn Luyện Mô Hình ALS</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <button class="btn btn-success mb-2 me-2" wire:click="triggerTraining" wire:loading.attr="disabled">
                        <span wire:loading wire:target="triggerTraining">Đang kích hoạt...</span>
                        <span wire:loading.remove wire:target="triggerTraining">Huấn Luyện Ngay</span>
                    </button>
                    <button class="btn btn-warning mb-2" wire:loading.attr="disabled" wire:click="clearCache">
                        <span wire:loading wire:target="clearCache">Đang xóa...</span>
                        <span wire:loading.remove wire:target="clearCache">Xóa Cache Gợi Ý</span>
                    </button>
                </div>
                <div class="col-md-6">
                    <p class="text-info">{{ $trainingSchedule }}</p>
                    <button class="btn btn-info" wire:click="loadTrainingSchedule" wire:loading.attr="disabled">
                        <span wire:loading wire:target="loadTrainingSchedule">Đang tải...</span>
                        <span wire:loading.remove wire:target="loadTrainingSchedule">Xem Lịch Huấn Luyện</span>
                    </button>
                    <div class="mb-3">
                        <label for="selectedModel" class="form-label">Chọn Mô Hình</label>
                        <select class="form-select" wire:model="selectedModel" id="selectedModel">
                            <option value="">Chọn mô hình...</option>
                            @foreach ($models as $model)
                                <option value="{{ $model['path'] }}">{{ $model['name'] }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-info mt-2" wire:click="selectModel" wire:loading.attr="disabled">
                            <span wire:loading wire:target="selectModel">Đang chọn...</span>
                            <span wire:loading.remove wire:target="selectModel">Chọn Mô Hình</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tiến Trình Huấn Luyện</label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar"
                         style="width: {{ $progress }}%;"
                         aria-valuenow="{{ $progress }}"
                         aria-valuemin="0"
                         aria-valuemax="100">{{ $progress }}%</div>
                </div>
            </div>
            <p class="text-info">{{ $trainingStatus }}</p>
            @if ($trainingError)
                <div class="text-danger mb-3">{{ $trainingError }}</div>
            @endif
        </div>
    </div>
</div>

      <div class="tab-pane fade {{ $activeSection == 'statistic' ? 'show active' : '' }}" id="statistic" role="tabpanel" style="margin-left: 20px; margin-right: 20px">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thống Kê Hiệu Suất Gợi Ý</h5>
        </div>
        <div class="card-body">
            <!-- Hàng 1: Thống kê tổng quan -->
            <div class="row mb-4">
                <div class="col-md-6 col-sm-6 mb-3">
                    <div class="stat-card">
                        <h6>Tổng Người Dùng Tương Tác</h6>
                        <div class="value">{{ $totalUsersInteracted }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 mb-3">
                    <div class="stat-card bg-light">
                        <h6>Gợi Ý Chuyển Đổi</h6>
                        <div class="value">{{ number_format($recommendationStats['converted_recommendations'] ?? 0) }}</div>
                        <div class="rate">
                            {{ isset($recommendationStats['cvr']) ? number_format($recommendationStats['cvr'], 2) : '0.00' }}% CVR
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hàng 2: Thống kê tương tác chi tiết -->
            <div class="row mb-4">
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="stat-card" style="background-color: #e3f2fd;">
                        <h6>Tổng Số Click</h6>
                        <div class="value">{{ number_format($interactionStats['total_clicks'] ?? 0) }}</div>
                        <div class="rate">
                            <i class="fas fa-mouse-pointer me-1"></i> {{ number_format($interactionStats['avg_clicks_per_user'] ?? 0, 2) }} clicks/người dùng
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="stat-card" style="background-color: #fff8e1;">
                        <h6>Tổng Add To Cart</h6>
                        <div class="value">{{ number_format($interactionStats['total_add_to_cart'] ?? 0) }}</div>
                        <div class="rate">
                            <i class="fas fa-cart-plus me-1"></i> 
                            {{ isset($interactionStats['cart_per_click']) ? number_format($interactionStats['cart_per_click'], 2) : '0.00' }}% tỷ lệ thêm giỏ
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="stat-card" style="background-color: #e8f5e9;">
                        <h6>Tổng Order</h6>
                        <div class="value">{{ number_format($interactionStats['total_orders'] ?? 0) }}</div>
                        <div class="rate">
                            <i class="fas fa-shopping-bag me-1"></i> 
                            {{ isset($interactionStats['order_per_cart']) ? number_format($interactionStats['order_per_cart'], 2) : '0.00' }}% tỷ lệ đặt hàng
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ -->
            <div class="row" wire:ignore>
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Hiệu Suất Tương Tác Theo Ngày</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div class="chart-loading" id="recommendationsByDayChart-loading">
                                    <div class="chart-loading-spinner"></div>
                                </div>
                                <canvas id="recommendationsByDayChart" style="width: 100%; height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Tỷ Lệ Chuyển Đổi Theo Danh Mục</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="conversionByCategoryChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Thống Kê Tương Tác</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="interactionStatsChart" style="width: 100%; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng thống kê chi tiết -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Chi Tiết Tương Tác Theo Loại</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Loại Tương Tác</th>
                                    <th>Số Lượng</th>
                                    <th>Tỷ Lệ (%)</th>
                                    <th>Giá Trị Trung Bình</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalInteractions = $totalInteractions ?? 0;
                                @endphp
                                @foreach($interactionTypeDistribution ?? [] as $type => $count)
                                <tr>
                                    <td>
                                        @if($type == 'click')
                                            <i class="fas fa-mouse-pointer me-1 text-primary"></i> Click
                                        @elseif($type == 'add_to_cart')
                                            <i class="fas fa-cart-plus me-1 text-warning"></i> Thêm vào giỏ
                                        @elseif($type == 'order')
                                            <i class="fas fa-shopping-bag me-1 text-success"></i> Đặt hàng
                                        @else
                                            <i class="fas fa-exchange-alt me-1 text-secondary"></i> {{ $type }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($count) }}</td>
                                    <td>{{ number_format(($count / $totalInteractions) * 100, 2) }}%</td>
                                    <td>{{ number_format($interactionStats['interaction_value_by_type'][$type] ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biến toàn cục để lưu trữ các biểu đồ
window.chartInstances = {};
window.modelCharts = {};

// Hàm hủy biểu đồ
function destroyChart(chartId) {
    if (window.chartInstances[chartId]) {
        window.chartInstances[chartId].destroy();
        window.chartInstances[chartId] = null;
    }
    if (window.modelCharts[chartId]) {
        window.modelCharts[chartId].destroy();
        window.modelCharts[chartId] = null;
    }
}

// Hàm hiển thị/ẩn loading
function showChartLoading(chartId, show = true) {
    const loadingElement = document.getElementById(chartId + '-loading');
    if (loadingElement) {
        loadingElement.style.display = show ? 'flex' : 'none';
    }
}

// Khởi tạo biểu đồ thống kê gợi ý
function initializeRecommendationStatsCharts() {
    const statisticTab = document.getElementById('statistic');
    if (!statisticTab || !statisticTab.classList.contains('active')) {
        return;
    }

    try {
        showChartLoading('recommendationsByDayChart', true);
        showChartLoading('conversionByCategoryChart', true);
        showChartLoading('interactionStatsChart', true);

        destroyChart('recommendationsByDayChart');
        destroyChart('conversionByCategoryChart');
        destroyChart('interactionStatsChart');

        const recommendationsByDay = @json($recommendationsByDay ?? []);
        const recommendationConversionRate = @json($recommendationConversionRate ?? []);
        const interactionTypeDistribution = @json($interactionTypeDistribution ?? []);

        // Biểu đồ tương tác theo ngày
        const recommendationsByDayCtx = document.getElementById('recommendationsByDayChart');
        if (recommendationsByDayCtx && recommendationsByDay.length > 0) {
            window.chartInstances['recommendationsByDayChart'] = new Chart(recommendationsByDayCtx, {
                type: 'line',
                data: {
                    labels: recommendationsByDay.map(item => item.date),
                    datasets: [
                        {
                            label: 'Lượt Click',
                            data: recommendationsByDay.map(item => item.clicked),
                            backgroundColor: 'rgba(59, 130, 246, 0.2)', // Xanh lam
                            borderColor: '#3B82F6',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Thêm vào giỏ',
                            data: recommendationsByDay.map(item => item.added_to_cart),
                            backgroundColor: 'rgba(245, 158, 11, 0.2)', // Cam
                            borderColor: '#F59E0B',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Đặt Hàng',
                            data: recommendationsByDay.map(item => item.converted),
                            backgroundColor: 'rgba(16, 185, 129, 0.2)', // Xanh ngọc
                            borderColor: '#10B981',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Hiệu suất tương tác theo ngày',
                            font: { size: 16, weight: 'bold' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Biểu đồ tỷ lệ chuyển đổi theo danh mục
        const conversionByCategoryCtx = document.getElementById('conversionByCategoryChart');
        if (conversionByCategoryCtx && recommendationConversionRate.length > 0) {
            window.chartInstances['conversionByCategoryChart'] = new Chart(conversionByCategoryCtx, {
                type: 'pie',
                data: {
                    labels: recommendationConversionRate.map(item => item.category),
                    datasets: [{
                        data: recommendationConversionRate.map(item => item.conversion_rate),
                        backgroundColor: [
                            '#EC4899', '#3B82F6', '#FCD34D', '#10B981', 
                            '#8B5CF6', '#F97316', '#14B8A6', '#6B7280'
                        ],
                        borderColor: [
                            '#EC4899', '#3B82F6', '#FCD34D', '#10B981', 
                            '#8B5CF6', '#F97316', '#14B8A6', '#6B7280'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tỷ lệ chuyển đổi theo danh mục (%)',
                            font: { size: 16, weight: 'bold' }
                        },
                        tooltip: {
                            callbacks: { 
                                label: context => `${context.label}: ${context.raw}%` 
                            }
                        },
                        legend: {
                            position: 'right',
                            labels: { padding: 20, boxWidth: 12 }
                        }
                    }
                }
            });
        }

        // Biểu đồ thống kê tương tác
        const interactionStatsCtx = document.getElementById('interactionStatsChart');
        if (interactionStatsCtx) {
            const labels = ['Lượt Click', 'Thêm vào giỏ', 'Đặt Hàng'];
            const data = [
                interactionTypeDistribution['click'] || 0,
                interactionTypeDistribution['add_to_cart'] || 0,
                interactionTypeDistribution['order'] || 0
            ];

            window.chartInstances['interactionStatsChart'] = new Chart(interactionStatsCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Số lượng tương tác',
                        data: data,
                        backgroundColor: ['#3B82F6', '#F59E0B', '#10B981'],
                        borderColor: ['#3B82F6', '#F59E0B', '#10B981'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true,
                            title: { display: true, text: 'Số lượng' }
                        },
                        x: { title: { display: true, text: 'Loại tương tác' } }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Thống kê tương tác theo loại',
                            font: { size: 16, weight: 'bold' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        setTimeout(() => {
            showChartLoading('recommendationsByDayChart', false);
            showChartLoading('conversionByCategoryChart', false);
            showChartLoading('interactionStatsChart', false);
        }, 500);
    } catch (error) {
        console.error('Error initializing recommendation stats charts:', error);
    }
}

// ... (giữ nguyên các hàm khác như initializeModelCharts, sự kiện DOM, v.v.)
function initializeModelCharts() {
    const evaluationTab = document.getElementById('evaluation');
    if (!evaluationTab || !evaluationTab.classList.contains('active')) {
        return;
    }

    try {
        const metrics = window.modelChartData || @json($metrics ?? []);
        const k = window.kValue || @json($k);

        if (!metrics || metrics.length === 0) {
            return;
        }

        const modelNames = metrics.map(item => item.method);
        const processedMetrics = metrics.map(item => ({
            ...item,
            diversity_at_k: item.diversity_at_k ?? 0,
            coverage_at_k: item.coverage_at_k ?? 0,
            precision_at_k: item.precision_at_k ?? 0,
            recall_at_k: item.recall_at_k ?? 0,
            ndcg_at_k: item.ndcg_at_k ?? 0
        }));

        destroyChart('diversityCoverageChart');
        destroyChart('precisionRecallChart');
        destroyChart('ndcgChart');
        destroyChart('modelRadarChart');

        // Biểu đồ Diversity & Coverage
        const diversityCoverageCtx = document.getElementById('diversityCoverageChart');
        if (diversityCoverageCtx) {
            window.modelCharts['diversityCoverageChart'] = new Chart(diversityCoverageCtx, {
                type: 'bar',
                data: {
                    labels: modelNames,
                    datasets: [
                        {
                            label: 'Diversity@' + k,
                            data: processedMetrics.map(item => item.diversity_at_k),
                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            borderColor: '#8B5CF6',
                            borderWidth: 1
                        },
                        {
                            label: 'Coverage@' + k,
                            data: processedMetrics.map(item => item.coverage_at_k),
                            backgroundColor: 'rgba(252, 211, 77, 0.7)',
                            borderColor: '#FCD34D',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 1 } }
                }
            });
        }

        // Biểu đồ Precision & Recall
        const precisionRecallCtx = document.getElementById('precisionRecallChart');
        if (precisionRecallCtx) {
            window.modelCharts['precisionRecallChart'] = new Chart(precisionRecallCtx, {
                type: 'bar',
                data: {
                    labels: modelNames,
                    datasets: [
                        {
                            label: 'Precision@' + k,
                            data: processedMetrics.map(item => item.precision_at_k),
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: '#3B82F6',
                            borderWidth: 1
                        },
                        {
                            label: 'Recall@' + k,
                            data: processedMetrics.map(item => item.recall_at_k),
                            backgroundColor: 'rgba(236, 72, 153, 0.7)',
                            borderColor: '#EC4899',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 1 } }
                }
            });
        }

        // Biểu đồ NDCG
        const ndcgCtx = document.getElementById('ndcgChart');
        if (ndcgCtx) {
            window.modelCharts['ndcgChart'] = new Chart(ndcgCtx, {
                type: 'bar',
                data: {
                    labels: modelNames,
                    datasets: [
                        {
                            label: 'NDCG@' + k,
                            data: processedMetrics.map(item => item.ndcg_at_k),
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: '#10B981',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 1 } }
                }
            });
        }

        // Biểu đồ Radar
        const radarCtx = document.getElementById('modelRadarChart');
        if (radarCtx) {
            const colors = [
                { bg: 'rgba(236, 72, 153, 0.2)', border: '#EC4899' },
                { bg: 'rgba(59, 130, 246, 0.2)', border: '#3B82F6' },
                { bg: 'rgba(252, 211, 77, 0.2)', border: '#FCD34D' },
                { bg: 'rgba(16, 185, 129, 0.2)', border: '#10B981' },
                { bg: 'rgba(139, 92, 246, 0.2)', border: '#8B5CF6' },
                { bg: 'rgba(245, 158, 11, 0.2)', border: '#F59E0B' }
            ];

            const datasets = processedMetrics.map((item, index) => {
                const color = colors[index % colors.length];
                return {
                    label: item.method,
                    data: [
                        item.precision_at_k,
                        item.recall_at_k,
                        item.ndcg_at_k,
                        item.diversity_at_k,
                        item.coverage_at_k
                    ],
                    backgroundColor: color.bg,
                    borderColor: color.border,
                    pointBackgroundColor: color.border,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: color.border
                };
            });

            window.modelCharts['modelRadarChart'] = new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: ['Precision@' + k, 'Recall@' + k, 'NDCG@' + k, 'Diversity@' + k, 'Coverage@' + k],
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { r: { beginAtZero: true, max: 1 } }
                }
            });
        }
    } catch (error) {
        console.error('Error initializing model charts:', error);
    }
}

// Lưu và khôi phục tab đã chọn
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#recommendationTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            const section = this.getAttribute('wire:click').match(/'([^']+)'/)[1];
            localStorage.setItem('recommendationActiveTab', section);
        });
    });

    const statisticTab = document.getElementById('statistic');
    if (statisticTab && statisticTab.classList.contains('show') && statisticTab.classList.contains('active')) {
        setTimeout(() => {
            initializeRecommendationStatsCharts();
        }, 300);
    }

    const evaluationTab = document.getElementById('evaluation');
    if (evaluationTab && evaluationTab.classList.contains('show') && evaluationTab.classList.contains('active')) {
        setTimeout(() => {
            initializeModelCharts();
        }, 300);
    }
});

// Cập nhật biểu đồ khi chuyển tab hoặc Livewire cập nhật
document.addEventListener('active-section-changed', function() {
    setTimeout(() => {
        initializeRecommendationStatsCharts();
        initializeModelCharts();
    }, 100);
});

document.addEventListener('livewire:update', function() {
    setTimeout(() => {
        initializeRecommendationStatsCharts();
        initializeModelCharts();
    }, 100);
});

// Kiểm tra thư viện
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded!');
}
if (typeof Chart === 'undefined') {
    console.error('Chart.js is not loaded!');
}
if (typeof Livewire === 'undefined') {
    console.error('Livewire is not defined');
}

// Định nghĩa biến dailyOrdersOptions để tránh lỗi
window.addEventListener('DOMContentLoaded', function() {
    if (typeof dailyOrdersOptions === 'undefined') {
        window.dailyOrdersOptions = {
            series: [],
            chart: { type: 'bar', height: 300 }
        };
    }
});

document.addEventListener('metricsUpdated', function (event) {
    const { k, metrics } = event.detail;

    // Gán lại giá trị toàn cục để render lại biểu đồ
    window.modelChartData = metrics;
    window.kValue = k;

    // Cập nhật tiêu đề
    document.getElementById('modelRadarChart-title').innerText = `So Sánh Tổng Quan Các Mô Hình @k=${k}`;
    document.getElementById('precisionRecallChart-title').innerText = `Precision & Recall @k=${k}`;
    document.getElementById('ndcgChart-title').innerText = `NDCG @k=${k}`;
    document.getElementById('diversityCoverageChart-title').innerText = `Diversity & Coverage @k=${k}`;

    // Vẽ lại biểu đồ
    initializeModelCharts();
});

document.addEventListener('DOMContentLoaded', function() {
    // Đặt tab thống kê làm mặc định trong localStorage
    localStorage.setItem('recommendationActiveTab', 'statistic');
    
    // Đảm bảo tab thống kê được hiển thị
    const statisticTab = document.getElementById('statistic-tab');
    if (statisticTab) {
        statisticTab.click();
    }
    
    // Khởi tạo biểu đồ thống kê
    setTimeout(() => {
        initializeRecommendationStatsCharts();
    }, 300);
});
</script>
@endpush
