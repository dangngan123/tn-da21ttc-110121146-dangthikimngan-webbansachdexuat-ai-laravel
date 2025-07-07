<div>
    <main class="main">
        <!-- CSS -->
       <style>
    /* Giữ nguyên các style cũ và bổ sung phong cách hiện đại */
    .notification-dropdown {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .notification-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .notification-item:hover {
        background-color: #f1faff;
    }

    .notification-item.unread {
        background-color: #e6f3ff;
    }

    .notification-item .time {
        font-size: 0.8em;
        color: #6c757d;
    }
.chart-container {
                position: relative;
                margin-bottom: 20px;
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                min-height: 420px; /* Chiều cao tối thiểu */
                max-height: 420px; /* Chiều cao tối đa cố định */
            }

            .chart-container canvas {
                height: 300px !important; /* Đảm bảo chiều cao cố định */
            }
   /* Điều chỉnh kích thước và phong cách của icon-stat với màu nền tươi sáng */
.icon-stat {
    overflow: hidden;
    position: relative;
    padding: 15px;
    margin-bottom: 1em;
    background-color: #fff; /* Nền trắng làm nền chính */
    border-radius: 12px;
    border: 1px solid #e3e7eb;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); /* Thêm bóng nhẹ */
}

/* Màu nền tươi sáng cho từng loại thống kê */
.stat-today-revenue {
    background: linear-gradient(135deg,rgb(255, 5, 234),rgb(158, 148, 198)); /* Đỏ tươi */
}

.stat-processing {
    background: linear-gradient(135deg,rgb(98, 255, 0),rgb(4, 89, 25)); /* Vàng sáng */
}

.stat-total-stock {
    background: linear-gradient(135deg, #4bc0c0,rgb(255, 239, 11)); /* Xanh ngọc */
}

.stat-total-customers {
    background: linear-gradient(135deg,rgb(1, 217, 255),rgb(230, 233, 165)); /* Tím phấn */
}

/* Điều chỉnh label */
.icon-stat-label {
    display: block;
    color: #ffffff; /* Chữ trắng để nổi bật trên nền gradient */
    font-size: 12px;
    font-weight: 500;
}

/* Điều chỉnh giá trị */
.icon-stat-value {
    display: block;
    font-size: 20px;
    font-weight: 700;
    color: #ffffff; /* Chữ trắng để nổi bật */
    text-align: center;
    margin: 5px 0;
}

/* Điều chỉnh biểu tượng */
.icon-stat-visual {
    position: absolute;
    top: 10px;
    right: 10px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    text-align: center;
    font-size: 18px;
    color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.icon-stat-visual:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Điều chỉnh footer (phần trăm tăng trưởng) */
.icon-stat-footer {
    padding: 0;
    margin-top: 5px;
    color: #ffffff; /* Chữ trắng để nổi bật */
    font-size: 12px;
    text-align: center;
    border-top: none;
}

/* Màu sắc cho icon-stat-visual (giữ nguyên nhưng cập nhật nếu cần) */
.bg-today-revenue { background: #ff6b6b; }
.bg-processing { background:rgb(145, 114, 5); }
.bg-total-stock { background: #4bc0c0; }
.bg-new-customers-month { background: #ab47bc; }

.icon-stat:hover {
    transform: translateY(-3px); /* Hiệu ứng hover */
}

/* Điều chỉnh label */
.icon-stat-label {
    display: block;
    color:rgb(0, 0, 0);
    font-size: 12px; /* Giảm kích thước chữ nhãn */
    font-weight: 500;
  
}

/* Điều chỉnh giá trị */
.icon-stat-value {
    display: block;
    font-size: 20px; /* Tăng kích thước chữ giá trị để nổi bật */
    font-weight: 700;
    color:rgb(255, 255, 255);
    text-align: center; /* Căn giữa giá trị */
    margin: 5px 0; /* Giữ khoảng cách hợp lý */
}

/* Điều chỉnh biểu tượng */
.icon-stat-visual {
    position: absolute;
    top: 10px;
    right: 10px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 40px; /* Tăng kích thước biểu tượng */
    height: 40px; /* Tăng kích thước biểu tượng */
    border-radius: 50%;
    text-align: center;
    font-size: 18px; /* Tăng kích thước font biểu tượng */
    color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.icon-stat-visual:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Điều chỉnh footer (phần trăm tăng trưởng) */
.icon-stat-footer {
    padding: 0; /* Loại bỏ padding để tiết kiệm không gian */
    margin-top: 5px;
    color:rgb(0, 0, 0); /* Màu xanh lá */
    font-size: 12px; /* Kích thước chữ nhỏ */
    text-align: center; /* Căn giữa */
    border-top: none; /* Loại bỏ đường viền trên */
}

    /* Màu sắc tươi sáng mới cho icon-stat-visual */
    .bg-today-revenue { background:rgb(69, 69, 69); } /* Đỏ tươi */
    .bg-processing { background:rgb(69, 69, 69); } /* Vàng sáng */
    .bg-total-stock { background:rgb(69, 69, 69); } /* Xám xanh */
    .bg-new-customers-month { background: rgb(69, 69, 69); } /* Tím phấn */
    
   .dashboard-menu {
    position: relative;
    width: 200px;
    background-color: #fff;
    border-right: 1px solid #ddd;
    overflow-y: auto; /* Giữ cuộn dọc */
    padding: 10px;
    height: calc(100vh - 120px);
    position: sticky;
    top: 70px;
    margin-left: -10px;
    border-radius: 0 10px 10px 0;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    /* Ẩn thanh lăn */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer và Edge */
}

/* Ẩn thanh lăn trên Chrome, Safari và các trình duyệt dựa trên WebKit */
.dashboard-menu::-webkit-scrollbar {
    display: none;
}

    .dashboard-menu .nav-link i {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        margin-right: 10px;
        color: #fff;
        font-size: 16px;
    }

    .icon-dashboard { background-color: #FF5733; }
    .icon-slider { background-color: #00C4B4; }
    .icon-category { background-color: #FFC107; }
    .icon-product { background-color: #007BFF; }
    .icon-order { background-color: #6F42C1; }
    .icon-coupons { background-color: #17A2B8; }
    .icon-customer { background-color: #E83E8C; }
    .icon-review { background-color: #FD7E14; }
    .icon-contact { background-color: #6C757D; }
    .icon-chatbot { background-color: #20C997; }
    .icon-sale { background-color: #B794F4; }
    .icon-statistics { background-color: #28A745; }
    .icon-recommendations { background-color: #FF5733; }

    .dashboard-menu .nav-link {
        transition: transform 0.2s ease, background-color 0.2s ease;
        color: #343a40;
        font-weight: 500;
        border-radius: 5px;
        padding: 10px 15px;
    }

    .dashboard-menu .nav-link:hover,
    .dashboard-menu .nav-link.active,
    .dashboard-menu .nav-link:active {
        transform: scale(1.05) translateX(5px);
        background-color: #f8f9fa;
        color: #007BFF;
    }

    .dashboard-menu .nav-link.active i {
        background-color: #FF3333;
    }

    .dashboard-content .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .dashboard-content .card-header {
        background-color: #f1faff;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
        padding: 15px;
        font-weight: 600;
        color: #1e2a44;
    }

    .dashboard-content .card-body {
        padding: 20px;
    }

    @media (max-width: 767px) {
        .dashboard-menu {
            width: 100%;
            height: auto;
            position: relative;
            top: 0;
        }

        .dashboard-content .icon-stat,
        .dashboard-content .card {
            max-width: 100%;
        }
    }

    .chart-container {
    position: relative;
    margin-bottom: 20px;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    min-height: 350px; /* Chiều cao tối thiểu của khung */
    max-height: 350px; /* Chiều cao tối đa cố định */
}

.chart-container canvas {
    height: 250px !important; /* Đảm bảo chiều cao cố định của canvas */
}

    .table-responsive {
        overflow-x: auto;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f1faff;
    }

    .table-striped tbody tr:hover {
        background-color: #e3e7eb;
    }





    /* Tạo container cho header với gradient nền và kích thước đồng đều */
.dashboard-header {
 background: linear-gradient(90deg, #dc3545, #ff6f61, #ff9f43);/* Gradient từ đỏ cam đậm sang cam sáng */
    padding: 12px 20px; /* Padding đồng đều */
    border-radius: 10px; /* Bo góc giống hình */
    margin-bottom: 20px; /* Khoảng cách với nội dung bên dưới */
    height: 100px; /* Chiều cao cố định để đồng đều */
    display: flex;
    margin-left: 20px;
    margin-right: 20px;
    align-items: center; /* Căn giữa theo chiều dọc */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Thêm bóng nhẹ để tách biệt */
    
}

/* Đảm bảo text nổi bật trên nền gradient */
h1.text-white {
    color: #ffffff;
    font-size: 24px; /* Kích thước chữ phù hợp với hình */
    font-weight: 600;
    margin: 0; /* Loại bỏ margin mặc định */
}

/* Điều chỉnh nút để đồng đều với tiêu đề */
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

/* Điều chỉnh cột để cân đối */
.col-md-8 {
    padding-left: 0; /* Loại bỏ padding thừa bên trái */
}

.col-md-4 {
    padding-right: 0; /* Loại bỏ padding thừa bên phải */
    text-align: right; /* Đảm bảo nút nằm sát mép phải */
}

/* Tùy chỉnh icon trong nút */
.fa-sync {
    font-size: 14px; /* Điều chỉnh kích thước icon */
}
   
</style>

        <section class="pt-10 pb-10">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 m-auto">
                        <div class="row g-1">
                            <!-- Menu -->
                            <div class="col-md-2">
                                <div class="dashboard-menu" id="dashboard-menu">
                                    <ul class="nav flex-column" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true"><i class="fi-rs-settings-sliders icon-dashboard"></i><span> Tổng quan</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="slider-tab" data-bs-toggle="tab" href="#slider" role="tab" aria-controls="slider" aria-selected="false"><i class="fa-regular fa-image icon-slider"></i><span> Slider</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="category-tab" data-bs-toggle="tab" href="#category" role="tab" aria-controls="category" aria-selected="false"><i class="fa-solid fa-list icon-category"></i><span> Danh mục</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="product-tab" data-bs-toggle="tab" href="#product" role="tab" aria-controls="product" aria-selected="false"><i class="fa-solid fa-box icon-product"></i><span> Sản phẩm</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="order-tab" data-bs-toggle="tab" href="#order" role="tab" aria-controls="order" aria-selected="false"><i class="fa-solid fa-cart-shopping icon-order"></i><span> Đơn hàng</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="coupons-tab" data-bs-toggle="tab" href="#coupons" role="tab" aria-controls="coupons" aria-selected="false"><i class="fa-solid fa-tag icon-coupons"></i><span> Khuyến mãi</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="customer-tab" data-bs-toggle="tab" href="#customer" role="tab" aria-controls="customer" aria-selected="false"><i class="fa-solid fa-user icon-customer"></i><span> Khách hàng</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="review-tab" data-bs-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false"><i class="fa-solid fa-star icon-review"></i><span> Đánh giá</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false"><i class="fa-solid fa-address-book icon-contact"></i><span> Liên hệ</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="chatbot-tab" data-bs-toggle="tab" href="#chatbot" role="tab" aria-controls="chatbot" aria-selected="false"><i class="fa-solid fa-robot icon-chatbot"></i><span> ChatBot</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="sale-tab" data-bs-toggle="tab" href="#sale" role="tab" aria-controls="sale" aria-selected="false"><i class="fa-solid fa-bolt icon-sale"></i><span> Flash Sale</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="recommendations-tab" data-bs-toggle="tab" href="#recommendations" role="tab" aria-controls="recommendations" aria-selected="false"><i class="fa-solid fa-lightbulb icon-recommendations"></i><span> Gợi ý</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="statistics-tab" data-bs-toggle="tab" href="#statistics" role="tab" aria-controls="statistics" aria-selected="false"><i class="fa-solid fa-chart-line icon-statistics"></i><span> Thống kê</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="col-md-10">
                                <div class="tab-content dashboard-content">
                                    <div class="tab-pane fade active show" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                        <div class="content">
                                           
                                           
                            <!-- Thêm nút làm mới -->
     <div class="dashboard-header row align-items-center mb-4">
    <div class="col-md-8">
        <h1 class="mb-0 text-white">Tổng quan</h1>
        <p class="text-white mb-0">Quản lý và theo dõi hiệu suất cửa hàng</p>
    </div>
    <div class="col-md-4 text-end">
        <button class="btn btn-light btn-sm" wire:click="refreshData" wire:loading.attr="disabled" style="color: #1e2a44;">
            <i class="fa fa-sync me-1"></i> Làm mới dữ liệu
            <span wire:loading wire:target="refreshData">
                <i class="fa fa-spinner fa-spin"></i>
            </span>
        </button>
        @if(session('success'))
            <span class="text-success">{{ session('success') }}</span>
        @endif
    </div>
</div>
  <div class="container">

 <!-- Hàng 1: Doanh thu, Đơn hàng, Sản phẩm, Người dùng -->
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="icon-stat stat-today-revenue">
            <div class="row">
                <div class="col-xs-8 text-left">
                    <span class="icon-stat-label">Tổng doanh thu</span>
                    <span class="icon-stat-value">{{ number_format($todayRevenue, 3, ',', '.') }} đ</span>
                </div>
                <div class="col-xs-4 text-center">
                    <i class="fa fa-dollar icon-stat-visual bg-today-revenue"></i>
                </div>
            </div>
            <div class="icon-stat-footer">
                <i class="fa fa-clock-o"></i> 
                @if($monthGrowth >= 0)
                    Tăng: <span style="color: #ffffff;"> {{ number_format($monthGrowth, 2) }}% </span>vs tháng trước
                @else
                    Giảm: <span style="color: #ffffff;">{{ number_format(abs($monthGrowth), 2) }}% </span>vs tháng trước
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="icon-stat stat-processing">
            <div class="row">
                <div class="col-xs-8 text-left">
                    <span class="icon-stat-label">Đơn hàng</span>
                    <span class="icon-stat-value">{{ $totalOrders }}</span>
                </div>
                <div class="col-xs-4 text-center">
                    <i class="fa fa-cart-shopping icon-stat-visual bg-processing"></i>
                </div>
            </div>
            <div class="icon-stat-footer">
                <i class="fa fa-clock-o"></i> 
                @if($weekGrowth >= 0)
                    Tăng: <span style="color: #ffffff;">{{ number_format($weekGrowth, 2) }}% </span>vs tuần trước
                @else
                    Giảm: <span style="color: #ffffff;">{{ number_format(abs($weekGrowth), 2) }}% </span>vs tuần trước
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="icon-stat stat-total-stock">
            <div class="row">
                <div class="col-xs-8 text-left">
                    <span class="icon-stat-label">Sản phẩm</span>
                    <span class="icon-stat-value">{{ $totalStock }}</span>
                </div>
                <div class="col-xs-4 text-center">
                    <i class="fa fa-box icon-stat-visual bg-total-stock"></i>
                </div>
            </div>
            <div class="icon-stat-footer">
                <i class="fa fa-clock-o"></i> 
                <?php
                    $productGrowth = ($newProducts / ($totalStock + $newProducts)) * 100;
                ?>
                @if($productGrowth >= 0)
                    Tăng: <span style="color: #ffffff;">{{ number_format($productGrowth, 2) }}%</span> vs trước
                @else
                    Giảm: <span style="color: #ffffff;">{{ number_format(abs($productGrowth), 2) }}% </span> vs trước
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="icon-stat stat-total-customers">
            <div class="row">
                <div class="col-xs-8 text-left">
                    <span class="icon-stat-label">Người dùng</span>
                    <span class="icon-stat-value">{{ $totalCustomers }}</span>
                </div>
                <div class="col-xs-4 text-center">
                    <i class="fas fa-user-group icon-stat-visual bg-new-customers-month"></i>
                </div>
            </div>
            <div class="icon-stat-footer">
                <i class="fa fa-clock-o"></i> 
                <?php
                    $customerGrowth = ($newCustomersThisMonth / ($totalCustomers + $newCustomersThisMonth)) * 100;
                ?>
                @if($customerGrowth >= 0)
                    Tăng: <span style="color: #ffffff;">{{ number_format($customerGrowth, 2) }}%</span> vs trước
                @else
                    Giảm: <span style="color: #ffffff;">{{ number_format(abs($customerGrowth), 2) }}%</span> vs trước
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Biểu đồ mới cho trạng thái đơn hàng -->
    <div class="row mt-5">
        <!-- Biểu đồ Đang xử lý -->
        <div class="col-md-3 col-sm-6 mb-4" > 
            <div class="card chart-container">
                <div class="card-header text-center" >
                    <h6>Đang xử lý</h6>
                </div>
                <div class="card-body">
                    <canvas id="processingOrdersChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Đang vận chuyển -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card chart-container">
                <div class="card-header text-center">
                    <h6>Đang vận chuyển</h6>
                </div>
                <div class="card-body">
                    <canvas id="shippedOrdersChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Đã giao hàng -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card chart-container">
                <div class="card-header text-center">
                    <h6>Đã giao hàng</h6>
                </div>
                <div class="card-body">
                    <canvas id="deliveredOrdersChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Đã hủy -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card chart-container">
                <div class="card-header text-center">
                    <h6>Đã hủy</h6>
                </div>
                <div class="card-body">
                    <canvas id="canceledOrdersChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                </div>
            </div>
        </div>
    </div>

    

                                                 <div class="row mt-5">
                                                    <!-- Biểu đồ đơn hàng theo ngày -->
                                                    <div class="col-md-6 col-sm-12 mb-4">
                                                        <div class="card chart-container">
                                                            <div class="card-header text-center">
                                                                <h6>Đơn hàng theo ngày</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="ordersPerDayChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Biểu đồ doanh thu tháng -->
                                                    <div class="col-md-6 col-sm-12 mb-4">
                                                        <div class="card chart-container">
                                                            <div class="card-header text-center">
                                                                <h6>Doanh thu theo tháng</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="monthlyRevenueChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <!-- Biểu đồ mới -->
                                                <div class="row mt-5">
                                                    <!-- Biểu đồ phân bố đánh giá -->
                                                    <div class="col-md-6 col-sm-12 mb-4">
                                                        <div class="card chart-container">
                                                            <div class="card-header text-center">
                                                                <h6>Phân bố đánh giá</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="ratingDistributionChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Biểu đồ trạng thái đơn hàng -->
                                                    <div class="col-md-6 col-sm-12 mb-4">
    <div class="card chart-container">
        <div class="card-header text-center">
            <h6>Phân bố doanh thu theo danh mục</h6>
        </div>
        <div class="card-body">
            <canvas id="categoryRevenueChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
        </div>
    </div>
</div>
                                                </div>

                                               

<div class="col-md-6 col-sm-12 mb-4">
    <div class="card chart-container">
        <div class="card-header text-center">
            <h6>Khách hàng mới theo tháng</h6>
        </div>
        <div class="card-body">
            <canvas id="newCustomersPerMonthChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
        </div>
    </div>
</div>

<div class="col-md-6 col-sm-12 mb-4">
    <div class="card chart-container">
        <div class="card-header text-center">
            <h6>Tỷ lệ khách hàng quay lại</h6>
        </div>
        <div class="card-body">
            <canvas id="customerRetentionChart" style="width: 100%; height: 300px;" wire:ignore></canvas>
        </div>
    </div>
</div>
<div class="row mt-5">
                                                    <!-- Top 5 sản phẩm bán chạy nhất -->
                                                  
                                                    <div class="col-md-6 col-sm-12 mb-4">
                                                        <div class="card">
                                                            <div class="card-header text-center">
                                                                <h6>Top 5 sản phẩm bán chạy nhất</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                @if(empty($topProducts))
                                                                    <p class="text-center">Chưa có dữ liệu sản phẩm bán chạy.</p>
                                                                @else
                                                                    <ul class="list-group">
                                                                        @foreach($topProducts as $index => $product)
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <span>{{ $index + 1 }}. {{ $product['name'] }}</span>
                                                                                <span class="badge bg-primary rounded-pill">{{ $product['total_sold'] }} sản phẩm</span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Top 5 khách hàng hoạt động nhiều nhất -->
                                                    <div class="col-md-6 col-sm-12 mb-4">
                                                        <div class="card">
                                                            <div class="card-header text-center">
                                                                <h6>Top 5 khách hàng hoạt động nhiều nhất</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                @if(empty($topCustomers))
                                                                    <p class="text-center">Chưa có dữ liệu khách hàng.</p>
                                                                @else
                                                                    <ul class="list-group">
                                                                        @foreach($topCustomers as $index => $customer)
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <span>{{ $index + 1 }}. {{ $customer['name'] }} ({{ $customer['email'] }})</span>
                                                                                <span class="badge bg-success rounded-pill">{{ $customer['order_count'] }} đơn hàng</span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <!-- Biểu đồ đơn hàng gần đây -->
                                                    <div class="col-md-12 col-sm-12 mb-4">
                                                        <div class="card">
                                                            <div class="card-header text-center">
                                                                <h6>Đơn hàng gần đây</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Đơn hàng</th>
                                                                                <th>Khách hàng</th>
                                                                                <th>Ngày</th>
                                                                                <th>Số tiền</th>
                                                                                <th>Trạng thái</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($orders as $order)
                                                                            <tr>
                                                                                <td>{{ $order->id }}</td>
                                                                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                                                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                                                <td>{{ number_format($order->total, 3, ',', '.') }} đ</td>
                                                                                <td>
                                                                                   @if($order->status == 'ordered')
    <span class="badge bg-primary">Đã đặt</span>
@elseif($order->status == 'processing')
    <span class="badge bg-warning">Đang xử lý</span>
@elseif($order->status == 'shipped')
    <span class="badge bg-info">Đang vận chuyển</span>
@elseif($order->status == 'delivered')
    <span class="badge bg-success">Đã giao</span>
@else
    <span class="badge bg-danger">Đã hủy</span>
@endif
                                                                                </td>
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
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="slider" role="tabpanel" aria-labelledby="slider-tab">
                                        @livewire('admin.manage-slider-component')
                                    </div>
                                    <div class="tab-pane fade" id="category" role="tabpanel" aria-labelledby="category-tab">
                                        @livewire('admin.manage-category-component')
                                    </div>
                                    <div class="tab-pane fade" id="product" role="tabpanel" aria-labelledby="product-tab">
                                        @livewire('admin.manage-product-component')
                                    </div>
                                    <div class="tab-pane fade" id="order" role="tabpanel" aria-labelledby="order-tab">
                                        @livewire('admin.manage-order-component')
                                    </div>
                                    <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                                        @livewire('admin.manage-customers-component')
                                    </div>
                                    <div class="tab-pane fade" id="coupons" role="tabpanel" aria-labelledby="coupons-tab">
                                        @livewire('admin.manage-coupons-component')
                                    </div>
                                    <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                                        @livewire('admin.manage-statistics-component')
                                    </div>
                                    <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                        @livewire('admin.manage-review-component')
                                    </div>
                                    <div class="tab-pane fade" id="chatbot" role="tabpanel" aria-labelledby="chatbot-tab">
                                        @livewire('admin.manage-chatbot-component')
                                    </div>
                                    <div class="tab-pane fade" id="sale" role="tabpanel" aria-labelledby="sale-tab">
                                        @livewire('admin.manage-sale-component')
                                    </div>
                                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                        @livewire('admin.manage-contact-component')
                                    </div>
                                    <div class="tab-pane fade" id="recommendations" role="tabpanel" aria-labelledby="recommendations-tab">
                                        @livewire('admin.manage-recommendations-component')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    let charts = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Khôi phục tab từ localStorage
        const activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            document.querySelectorAll('.nav-link.active').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-pane.active.show').forEach(pane => pane.classList.remove('active', 'show'));
            const targetTab = document.querySelector(`a[href="${activeTab}"]`);
            if (targetTab) {
                targetTab.classList.add('active');
                document.querySelector(activeTab).classList.add('active', 'show');
            }
        }

        // Lưu tab được chọn
        document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('click', function() {
                localStorage.setItem('activeTab', this.getAttribute('href'));
            });
        });

        // Hàm khởi tạo biểu đồ
        function initializeCharts() {
            // Biểu đồ phân bố đánh giá (Pie Chart)
            const ratingDistributionCtx = document.getElementById('ratingDistributionChart')?.getContext('2d');
            if (ratingDistributionCtx && !charts.ratingDistributionChart) {
                charts.ratingDistributionChart = new Chart(ratingDistributionCtx, {
                    type: 'pie',
                    data: {
                        labels: ['1 Sao', '2 Sao', '3 Sao', '4 Sao', '5 Sao'],
                        datasets: [{
                            data: [
                                {{ $userRatings[1] ?? 5 }},
                                {{ $userRatings[2] ?? 8 }},
                                {{ $userRatings[3] ?? 14 }},
                                {{ $userRatings[4] ?? 33 }},
                                {{ $userRatings[5] ?? 40 }}
                            ],
                            backgroundColor: ['#FF6B6B', '#FFD740', '#4BC0C0', '#40C4FF', '#7C4DFF'],
                            borderColor: ['#fff', '#fff', '#fff', '#fff', '#fff'],
                            borderWidth: 2,
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }

            // Biểu đồ trạng thái đơn hàng (Bar Chart)
            const orderStatusCtx = document.getElementById('orderStatusChart')?.getContext('2d');
            if (orderStatusCtx && !charts.orderStatusChart) {
                charts.orderStatusChart = new Chart(orderStatusCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Đã giao', 'Đang xử lý', 'Đang vận chuyển', 'Đã hủy'],
                        datasets: [{
                            label: 'Số đơn hàng',
                            data: [
                                {{ $deliveredCount ?? 0 }},
                                {{ $processingCount ?? 0 }},
                                {{ $shippedCount ?? 0 }},
                                {{ $canceledCount ?? 0 }}
                            ],
                            backgroundColor: ['#26A69A', '#ffc107', '#FF8A65', '#FF6B6B'],
                            borderColor: ['#26A69A', '#ffc107', '#FF8A65', '#FF6B6B'],
                            borderWidth: 1,
                            hoverBackgroundColor: ['#2ECC71', '#FFD740', '#FF9F40', '#FF4081']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        return `Số đơn hàng: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e3e7eb' },
                                ticks: {
                                    font: { size: 12 },
                                    color: '#1e2a44'
                                },
                                title: {
                                    display: true,
                                    text: 'Số đơn hàng',
                                    font: { size: 14, weight: '600' },
                                    color: '#1e2a44'
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 12 },
                                    color: '#1e2a44'
                                },
                                title: {
                                    display: true,
                                    text: 'Trạng thái',
                                    font: { size: 14, weight: '600' },
                                    color: '#1e2a44'
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

           // Biểu đồ đơn hàng theo ngày (Bar Chart) với màu riêng cho mỗi ngày
const ordersPerDayData = @json($ordersPerDay);
const dayMapping = {
    'Monday': 'Thứ 2',
    'Tuesday': 'Thứ 3',
    'Wednesday': 'Thứ 4',
    'Thursday': 'Thứ 5',
    'Friday': 'Thứ 6',
    'Saturday': 'Thứ 7',
    'Sunday': 'Chủ nhật'
};
const labels = ordersPerDayData.map(item => dayMapping[item.date]);
const data = ordersPerDayData.map(item => item.count);

// Mảng màu cho từng ngày trong tuần
const colors = [
    '#FF6B6B', // Thứ 2 - Đỏ
    '#FFD740', // Thứ 3 - Vàng
    '#4BC0C0', // Thứ 4 - Xanh ngọc
    '#40C4FF', // Thứ 5 - Xanh dương
    '#7C4DFF', // Thứ 6 - Tím
    '#AB47BC', // Thứ 7 - Tím đậm
    '#FF8A65'  // Chủ nhật - Cam
];

const ordersPerDayCtx = document.getElementById('ordersPerDayChart')?.getContext('2d');
if (ordersPerDayCtx && !charts.ordersPerDayChart) {
    charts.ordersPerDayChart = new Chart(ordersPerDayCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tổng số đơn hàng',
                data: data,
                backgroundColor: colors, // Áp dụng mảng màu cho từng cột
                borderColor: colors, // Áp dụng mảng màu cho viền
                borderWidth: 1,
                hoverBackgroundColor: colors.map(color => color.replace('0.8', '1')) // Tăng độ sáng khi hover
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e2a44',
                    titleFont: { size: 14 },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            return `Số đơn hàng: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 },
                        color: '#1e2a44'
                    },
                    grid: { color: '#e3e7eb' },
                    title: {
                        display: true,
                        text: 'Số đơn hàng',
                        font: { size: 14, weight: '600' },
                        color: '#1e2a44'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12 },
                        color: '#1e2a44'
                    },
                    title: {
                        display: true,
                        text: 'Ngày trong tuần',
                        font: { size: 14, weight: '600' },
                        color: '#1e2a44'
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
}

            // Biểu đồ doanh thu theo tháng (Line Chart)
            const monthlyRevenueData = @json($monthlyRevenueData);
            const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart')?.getContext('2d');
            if (monthlyRevenueCtx && !charts.monthlyRevenueChart) {
                charts.monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                        datasets: [{
                            label: 'Doanh thu',
                            data: [
                                monthlyRevenueData.T1 ?? 0,
                                monthlyRevenueData.T2 ?? 0,
                                monthlyRevenueData.T3 ?? 0,
                                monthlyRevenueData.T4 ?? 0,
                                monthlyRevenueData.T5 ?? 0,
                                monthlyRevenueData.T6 ?? 0,
                                monthlyRevenueData.T7 ?? 0,
                                monthlyRevenueData.T8 ?? 0,
                                monthlyRevenueData.T9 ?? 0,
                                monthlyRevenueData.T10 ?? 0,
                                monthlyRevenueData.T11 ?? 0,
                                monthlyRevenueData.T12 ?? 0
                            ],
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: '#4BC0C0',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#4BC0C0',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        return `Doanh thu: ${context.raw.toLocaleString('vi-VN')} đ`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e3e7eb' },
                                ticks: {
                                    font: { size: 12 },
                                    color: '#1e2a44',
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN') + ' đ';
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Doanh thu (đ)',
                                    font: { size: 14, weight: '600' },
                                    color: '#1e2a44'
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 12 },
                                    color: '#1e2a44'
                                },
                                title: {
                                    display: true,
                                    text: 'Tháng',
                                    font: { size: 14, weight: '600' },
                                    color: '#1e2a44'
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            // Biểu đồ khách hàng mới theo tháng (Line Chart)
            const newCustomersPerMonthData = @json($newCustomersPerMonth);
            const newCustomersPerMonthCtx = document.getElementById('newCustomersPerMonthChart')?.getContext('2d');
            if (newCustomersPerMonthCtx && !charts.newCustomersPerMonthChart) {
                charts.newCustomersPerMonthChart = new Chart(newCustomersPerMonthCtx, {
                    type: 'line',
                    data: {
                        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                        datasets: [{
                            label: 'Khách hàng mới',
                            data: [
                                newCustomersPerMonthData.T1 ?? 0,
                                newCustomersPerMonthData.T2 ?? 0,
                                newCustomersPerMonthData.T3 ?? 0,
                                newCustomersPerMonthData.T4 ?? 0,
                                newCustomersPerMonthData.T5 ?? 0,
                                newCustomersPerMonthData.T6 ?? 0,
                                newCustomersPerMonthData.T7 ?? 0,
                                newCustomersPerMonthData.T8 ?? 0,
                                newCustomersPerMonthData.T9 ?? 0,
                                newCustomersPerMonthData.T10 ?? 0,
                                newCustomersPerMonthData.T11 ?? 0,
                                newCustomersPerMonthData.T12 ?? 0
                            ],
                            backgroundColor: 'rgba(32, 201, 151, 0.2)',
                            borderColor: '#2ECC71',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#2ECC71',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        return `Số khách hàng mới: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e3e7eb' },
                                ticks: {
                                    font: { size: 12 },
                                    color: '#1e2a44'
                                },
                                title: {
                                    display: true,
                                    text: 'Số khách hàng',
                                    font: { size: 14, weight: '600' },
                                    color: '#1e2a44'
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 12 },
                                    color: '#1e2a44'
                                },
                                title: {
                                    display: true,
                                    text: 'Tháng',
                                    font: { size: 14, weight: '600' },
                                    color: '#1e2a44'
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            // Biểu đồ tỷ lệ khách hàng quay lại (Pie Chart)
            const customerRetentionCtx = document.getElementById('customerRetentionChart')?.getContext('2d');
            if (customerRetentionCtx && !charts.customerRetentionChart) {
                charts.customerRetentionChart = new Chart(customerRetentionCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Khách hàng quay lại', 'Khách hàng mua 1 lần'],
                        datasets: [{
                            data: [
                                {{ $repeatCustomers ?? 0 }},
                                {{ ($totalCustomers - $repeatCustomers) ?? 0 }}
                            ],
                            backgroundColor: ['#2ECC71', '#FF4081'],
                            borderColor: ['#fff', '#fff'],
                            borderWidth: 2,
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }

            // Biểu đồ Đang xử lý (Doughnut Chart)
            const processingOrdersCtx = document.getElementById('processingOrdersChart')?.getContext('2d');
            if (processingOrdersCtx && !charts.processingOrdersChart) {
                charts.processingOrdersChart = new Chart(processingOrdersCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Đang xử lý', 'Khác'],
                        datasets: [{
                            data: [
                                {{ $processingCount ?? 0 }},
                                {{ ($totalOrders - $processingCount - $shippedCount - $deliveredCount - $canceledCount) ?? 0 }}
                            ],
                            backgroundColor: ['#ffc107', '#d3d3d3'],
                            borderColor: ['#ffc107', '#d3d3d3'],
                            borderWidth: 2,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 12, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }

            // Biểu đồ Đang vận chuyển (Doughnut Chart)
            const shippedOrdersCtx = document.getElementById('shippedOrdersChart')?.getContext('2d');
            if (shippedOrdersCtx && !charts.shippedOrdersChart) {
                charts.shippedOrdersChart = new Chart(shippedOrdersCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Đang vận chuyển', 'Khác'],
                        datasets: [{
                            data: [
                                {{ $shippedCount ?? 0 }},
                                {{ ($totalOrders - $processingCount - $shippedCount - $deliveredCount - $canceledCount) ?? 0 }}
                            ],
                            backgroundColor: ['#0cc0df ', '#d3d3d3'],
                            borderColor: ['#0cc0df ', '#d3d3d3'],
                            borderWidth: 2,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 12, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }

            // Biểu đồ Đã giao hàng (Doughnut Chart)
            const deliveredOrdersCtx = document.getElementById('deliveredOrdersChart')?.getContext('2d');
            if (deliveredOrdersCtx && !charts.deliveredOrdersChart) {
                charts.deliveredOrdersChart = new Chart(deliveredOrdersCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Đã giao hàng', 'Khác'],
                        datasets: [{
                            data: [
                                {{ $deliveredCount ?? 0 }},
                                {{ ($totalOrders - $processingCount - $shippedCount - $deliveredCount - $canceledCount) ?? 0 }}
                            ],
                            backgroundColor: ['#1e8e58', '#d3d3d3'],
                            borderColor: ['#1e8e58', '#d3d3d3'],
                            borderWidth: 2,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 12, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }

            // Biểu đồ Đã hủy (Doughnut Chart)
            const canceledOrdersCtx = document.getElementById('canceledOrdersChart')?.getContext('2d');
            if (canceledOrdersCtx && !charts.canceledOrdersChart) {
                charts.canceledOrdersChart = new Chart(canceledOrdersCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Đã hủy', 'Khác'],
                        datasets: [{
                            data: [
                                {{ $canceledCount ?? 0 }},
                                {{ ($totalOrders - $processingCount - $shippedCount - $deliveredCount - $canceledCount) ?? 0 }}
                            ],
                            backgroundColor: ['#ff3131', '#d3d3d3'],
                            borderColor: ['#ff3131', '#d3d3d3'],
                            borderWidth: 2,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: { size: 12, weight: '500' },
                                    color: '#1e2a44'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#1e2a44',
                                titleFont: { size: 14 },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }
        }





        // Biểu đồ phân bố doanh thu theo danh mục (Pie Chart)
const categoryRevenueData = @json($categoryRevenue);
const categoryRevenueCtx = document.getElementById('categoryRevenueChart')?.getContext('2d');
if (categoryRevenueCtx && !charts.categoryRevenueChart) {
    charts.categoryRevenueChart = new Chart(categoryRevenueCtx, {
        type: 'pie',
        data: {
            labels: categoryRevenueData.map(item => item.name), // Tên danh mục
            datasets: [{
                data: categoryRevenueData.map(item => item.revenue), // Doanh thu
                backgroundColor: ['#FF6B6B', '#FFD740', '#4BC0C0', '#40C4FF', '#7C4DFF', '#AB47BC', '#FF8A65'], // Màu sắc
                borderColor: ['#fff', '#fff', '#fff', '#fff', '#fff', '#fff', '#fff'],
                borderWidth: 2,
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { size: 14, weight: '500' },
                        color: '#1e2a44'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e2a44',
                    titleFont: { size: 14 },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total ? ((value / total) * 100).toFixed(2) : 0;
                            return `${context.label}: ${value.toLocaleString('vi-VN')} đ (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}

        // Khởi tạo biểu đồ khi trang tải
        initializeCharts();

        // Lắng nghe sự kiện Livewire để tái khởi tạo biểu đồ khi dữ liệu thay đổi
        Livewire.on('refreshCharts', () => {
            // Hủy các biểu đồ hiện tại
            Object.values(charts).forEach(chart => chart.destroy());
            charts = {}; // Xóa đối tượng charts
            initializeCharts(); // Tái khởi tạo biểu đồ với dữ liệu mới
        });
    });
</script>