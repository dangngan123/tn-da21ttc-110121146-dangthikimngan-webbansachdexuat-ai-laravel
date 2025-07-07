<div>
    <style>
        .stats-card {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 8px;
            margin-right: 10px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-card .badge {
            margin-right: 8px;
            font-size: 12px;
        }

        .stats-card span {
            font-weight: bold;
            color: #333;
        }

        .badge.bg-primary { background-color: #007bff; }
        .badge.bg-success { background-color: #28a745; }
        .badge.bg-danger { background-color: #dc3545; }
        .badge.bg-info { background-color: #17a2b8; }
        .badge.bg-purple { background-color: #6f42c1; }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="shop-product-fillter mb-0">
                <!-- Hàng 1: Phần thống kê người dùng -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stats-card">
                        <span class="badge bg-primary text-white">Tổng người dùng</span>
                        <span>{{ $totalUsers }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-success text-white">Hoạt động</span>
                        <span>{{ $activeUsers }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-danger text-white">Bị khóa</span>
                        <span>{{ $blockedUsers }}</span>
                    </div>
                    <div class="stats-card">
                        <span class="badge bg-info text-white">Mới hôm nay</span>
                        <span>{{ $newUsersToday }}</span>
                    </div>
                </div>

                <!-- Hàng 2: Form Bộ lọc -->
                <div class="sort-by-product-area align-items-center">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <input type="text" class="form-control" placeholder="Tìm kiếm (Tên, Email, SĐT)" wire:model.live="search" style="background-color: rgb(255, 255, 255); width: 500px; max-width: 100%;">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-control" wire:model.live="searchStatus" style="background-color: rgb(255, 255, 255);">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1">Đang hoạt động</option>
                                <option value="0">Không hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-control" wire:model.live="searchGender" style="background-color: rgb(255, 255, 255);">
                                <option value="">Tất cả giới tính</option>
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-control" wire:model.live="pagesize" style="background-color: rgb(255, 255, 255);">
                                <option value="5">5 bản ghi</option>
                                <option value="10">10 bản ghi</option>
                                <option value="20">20 bản ghi</option>
                                <option value="50">50 bản ghi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Bảng dữ liệu -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>STT</th>
                            <th>Mã khách hàng</th>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Giới tính</th>
                            <th>Ngày sinh</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        @if($user->utype !== 'admin')
                        <tr class="text-center">
                            <td>{{ $index + $users->firstItem() }}</td>
                            <td>#{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if($user->gender == 'male') Nam
                                @elseif($user->gender == 'female') Nữ
                                @elseif($user->gender == 'other') Khác
                                @else N/A
                                @endif
                            </td>
                            <td>{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @if($user->status == 1)
                                <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                <span class="badge bg-danger">Không hoạt động</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($user->status == 0)
                                <a href="#" class="btn-small d-block btn-sm btn-success" wire:click.prevent="unblockUser({{ $user->id }})">Mở</a>
                                @else
                                <a href="#" class="btn-small d-block btn-sm btn-danger" wire:click.prevent="blockUser({{ $user->id }})">Chặn</a>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-danger">Không có khách hàng nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>