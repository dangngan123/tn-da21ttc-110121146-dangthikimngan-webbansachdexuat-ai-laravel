<div>
    <style>
        .btn:hover {
            background-color: #dc3545 !important;
            /* Giữ màu đỏ mặc định */
            border-color: #dc3545 !important;
            color: #fff;
        }
    </style>
    <div class="container_body">

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-trash-fill me-2"></i>Xóa tài khoản</h5>
            </div>
            <div class="card-body">
                @if (session()->has('account_deleted'))
                <div class="alert alert-success mt-3 mb-0">
                    {{ session('account_deleted') }}
                </div>
                @elseif (session()->has('error'))
                <div class="alert alert-danger mt-3 mb-0">
                    {{ session('error') }}
                </div>
                @endif

                @if(!$showConfirmation)
                <div class="mb-4">
                    <p class="text-danger fw-bold">Cảnh báo: Hành động này không thể hoàn tác!</p>
                    <p>Khi xóa tài khoản:</p>
                    <ul>
                        <li>Tất cả thông tin cá nhân của bạn sẽ bị xóa</li>
                        <li>Bạn sẽ không thể đăng nhập lại</li>
                        <li>Mọi dữ liệu liên quan sẽ bị xóa vĩnh viễn</li>
                    </ul>
                    <button wire:click="showDeleteConfirmation" class="btn btn-warning w-100">
                        Tôi muốn xóa tài khoản
                    </button>
                </div>
                @else
                <div class="mb-3">
                    <label for="password" class="form-label">Nhập mật khẩu để xác nhận:</label>
                    <input type="password"
                        wire:model="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password">
                    @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="d-grid gap-2">
                    <button wire:click="deleteAccount" class="btn btn-danger w-100">
                        Xác nhận xóa tài khoản
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-secondary w-100">Hủy</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>