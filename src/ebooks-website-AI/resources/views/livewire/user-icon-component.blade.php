<div>
    <style>
        /* Nút "Đăng Nhập" */
        .btn-danger {
            background-color: #C12530;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Nút "Đăng Ký" */
        .btn-outline-custom {
            border-color: #C12530 !important;
            color: #C12530;
            background-color: transparent;
        }

        .btn-outline-custom:hover {
            background-color: rgb(255, 255, 255) !important;
            color: #dc3545 !important;
        }

        /* Avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
        }

        /* Cố định vị trí biểu tượng chevron-right */
        .dropdown-item.user-profile {
            position: relative;
            display: flex;
            align-items: center;
            padding-right: 30px;
        }

        .dropdown-item.user-profile .chevron-icon {
            position: absolute;
            right: 10px;
            font-size: 12px;
        }

        .dropdown-item.user-profile .user-name {
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <div class="header-action-icon-2">
        <a class="mini-user-icon" href="{{ auth()->check() ? (auth()->user()->utype == 'admin' ? route('admin.dashboard') : route('customer.dashboard')) : route('login') }}">
            <img alt="" src="{{ asset('/assets/imgs/theme/icons/user.svg') }}">
        </a>

        <div class="cart-dropdown-wrap cart-dropdown-hm2">
            <ul>
                <li class="nav-item d-flex flex-column align-items-start">
                    @auth
                    <!-- Nếu người dùng đã đăng nhập -->
                    <a href="{{ Auth::user()->utype == 'admin' ? route('customer.dashboard') : route('customer.dashboard') }}" class="dropdown-item user-profile d-flex align-items-center">
                        <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('assets/imgs/about/avatar-3.jpg') }}" alt="Avatar" class="user-avatar me-2">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <i class="fa fa-chevron-right chevron-icon"></i>
                    </a>
                </li>
                @if(Auth::user()->utype == 'admin')
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                @else
                <li>
                    <a class="dropdown-item" href="{{ route('customer.orders') }}">
                        <i class="fa fa-shopping-bag me-2"></i> Đơn hàng của bạn
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('wishlist') }}">
                        <i class="fa fa-heart" style="font-size: 14px;"></i> <span style="font-size: 14px;">Sản phẩm yêu thích</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('voucher') }}">
                        <i class="fa fa-gift" style="font-size: 14px;"></i> <span style="font-size: 14px;">Ví voucher</span>
                    </a>
                </li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="dropdown-item text-center"
                            style="color: #fff; background-color: #dc3545; padding: 6px 12px; font-size: 14px; border-radius: 4px; width: 100%;">
                            <i class="fa fa-sign-out-alt me-1"></i> Đăng Xuất
                        </button>
                    </form>
                </li>
                @else
                <!-- Nếu người dùng chưa đăng nhập -->
                <a href="{{ route('login') }}" class="btn btn-danger mb-2 text-white w-100">Đăng Nhập</a>
                <a href="{{ route('register') }}" class="btn btn-outline-custom w-100">Đăng Ký</a>
                @endauth
                </li>
            </ul>
        </div>
    </div>
</div>