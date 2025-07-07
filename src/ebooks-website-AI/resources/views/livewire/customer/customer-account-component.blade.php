<div>
    <div class="container_body">
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Hồ sơ cá nhân</h5>
            </div>
            <div class="card-body">
                <!-- Thông báo lỗi chung nếu có (cho các trường chính) -->
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form wire:submit.prevent="updateProfile" enctype="multipart/form-data">
                    <!-- Họ và tên -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" wire:model="name" id="name" class="form-control" placeholder="Nhập họ và tên">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <div class="input-group">
                            <input type="text" wire:model="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <input type="email" wire:model="email" id="email" class="form-control" disabled>
                            <!-- <button type="button" class="btn btn-outline-primary" wire:click="cancelOtp">Hủy</button> -->
                        </div>

                        @if($user->google_id)
                        <div class="alert alert-warning mt-2">
                            Tài khoản của bạn được liên kết với Google. Nếu thay đổi email, bạn có thể gặp vấn đề khi đăng nhập bằng Google. Hãy đảm bảo email mới khớp với tài khoản Google của bạn.
                        </div>
                        @endif

                        <!-- Email mới -->
                        <div class="mt-2">
                            <label for="new_email" class="form-label">Email mới</label>
                            <div class="input-group">
                                <input type="email" wire:model="new_email" id="new_email" class="form-control" placeholder="Nhập email mới" {{ $otp_sent ? 'disabled' : '' }}>
                                <button type="button" class="btn btn-outline-primary" wire:click.prevent="changeEmail">Gửi OTP</button>
                            </div>
                            @error('new_email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nếu OTP đã gửi -->
                        @if($otp_sent)
                        <div class="mt-2">
                            <label for="otp" class="form-label">Mã OTP</label>
                            <div class="input-group">
                                <input type="text" wire:model="otp" id="otp" class="form-control" placeholder="Nhập mã OTP">
                                <button type="button" class="btn btn-outline-primary" wire:click="verifyOtpAndUpdateEmail">Xác thực</button>
                            </div>
                            @if ($errors->has('otp')) <span class="text-danger small">{{ $errors->first('otp') }}</span> @endif
                        </div>
                        @endif
                    </div>

                    <!-- Giới tính -->
                    <div class="mb-3">
                        <label for="gender" class="form-label">Giới tính <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" wire:model="gender" id="gender_male" value="male" class="form-check-input">
                                <label for="gender_male" class="form-check-label">Nam</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" wire:model="gender" id="gender_female" value="female" class="form-check-input">
                                <label for="gender_female" class="form-check-label">Nữ</label>
                            </div>
                        </div>
                        @error('gender') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ngày sinh -->
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Birthday <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <input type="text" wire:model="day" id="day" class="form-control" placeholder="DD" maxlength="2">
                            <input type="text" wire:model="month" id="month" class="form-control" placeholder="MM" maxlength="2">
                            <input type="text" wire:model="year" id="year" class="form-control" placeholder="YYYY" maxlength="4">
                        </div>
                        @error('day') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('month') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('year') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nút Cập nhật -->
                    <button type="submit" class="btn btn-danger w-100">Lưu thay đổi</button>
                </form>

                <!-- Hiển thị thông báo dạng toast -->
                @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session()->has('warning'))
                <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>