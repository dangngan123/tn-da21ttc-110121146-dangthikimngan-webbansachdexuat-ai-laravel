<div>
    <div class="container_body">
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-lock-fill me-2"></i>Đổi mật khẩu</h5>
            </div>
            <div class="card-body">
                @if(Session::has('password_success'))
                <div class="alert alert-success mt-3 mb-0" role="alert">
                    {{ Session::get('password_success') }}
                </div>
                @endif
                @if(Session::has('password_error'))
                <div class="alert alert-danger mt-3 mb-0" role="alert">
                    {{ Session::get('password_error') }}
                </div>
                @endif

                <form wire:submit.prevent="changePassword">
                    <!-- Nếu người dùng không đăng ký bằng Google HOẶC đã đặt mật khẩu cục bộ, yêu cầu mật khẩu hiện tại -->
                    @if(!$user->google_id || $user->password)
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" id="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại"
                            wire:model="current_password">
                        @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    @else
                    <div class="alert alert-info mt-3 mb-3">
                        Tài khoản của bạn được đăng ký bằng Google. Bạn đang thiết lập mật khẩu cục bộ để có thể đăng nhập bằng email và mật khẩu.
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" id="password" class="form-control" placeholder="Nhập mật khẩu mới"
                            wire:model="password">
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" id="password_confirmation" class="form-control"
                            placeholder="Nhập lại mật khẩu" wire:model="password_confirmation">
                        @error('password_confirmation') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>