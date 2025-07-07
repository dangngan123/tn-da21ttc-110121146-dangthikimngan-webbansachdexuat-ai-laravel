<div>
    <style>
        .rank-box {
            padding: 10px;
            border-radius: 12px;
            background-color: #fff;
            max-width: 200px;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .rank-icon {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 50%;
            transition: opacity 0.3s ease;
        }

        .rank-title {
            background-color: #d3d3d3;
            color: #333;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .rank-point {
            font-size: 14px;
            color: #555;
            margin-bottom: 2px;
        }

        .rank-progress {
            font-size: 13px;
            color: #888;
        }

        .container_body {
            position: relative;
            width: 100%;
            max-width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .container_body img {
            width: 100%;
            height: 200px;
            border-radius: 12px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: -100px;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .info-box {
            flex: 1;
            background-color: rgb(247, 247, 247);
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .info-box h4 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .info-box p {
            color: #c10000;
            font-weight: bold;
            font-size: 18px;
            margin: 0;
        }

        .custom-tab {
            background-color: white;
            color: black;
            font-size: 9px;
            transition: color 0.3s;
        }

        .custom-tab:hover {
            color: rgb(220, 24, 24) !important;
        }

        .custom-tab.active {
            color: rgb(220, 24, 24) !important;
        }

        /* Style cho biểu tượng máy ảnh */
        .edit-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            font-size: 14px;
            color: #e74c3c;
            border-radius: 500px;
            padding: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Hiển thị biểu tượng khi hover */
        .position-relative:hover .edit-icon {
            opacity: 1;
        }

        /* Làm mờ avatar khi hover */
        .position-relative:hover .rank-icon {
            opacity: 0.5;
        }

        /* Style cho input file */
        .avatar-input {
            display: none;
        }

        /* Style cho avatar preview */
        .avatar-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }
    </style>
    <main class="main">
        <section class="pt-10 pb-10">
            <div class="container">
                <div class="row">
                    <div class="col-lg-30 m-auto">
                        <div class="row g-1">
                            <div class="col-md-3">
                                <div class="dashboard-menu">
                                    <ul class="nav flex-column" role="tablist">
                                        <li class="nav-item">
                                            <div class="rank-box text-center">
                                                <div class="position-relative">
                                                    <!-- Hiển thị avatar hiện tại hoặc ảnh xem trước -->
                                                    <img src="{{ $new_avatar ? $new_avatar->temporaryUrl() : ($avatar ? asset($avatar) : asset('assets/imgs/about/avatar-3.jpg')) }}"
                                                        alt="Avatar"
                                                        class="rank-icon mb-2"
                                                        width="100"
                                                        height="100"
                                                        style="border: 3px solid #e0e0e0;">
                                                    <!-- Biểu tượng máy ảnh -->
                                                    <div class="edit-icon" onclick="document.getElementById('avatar-input-rank-box').click();">
                                                        <i class="bi bi-camera-fill"></i>
                                                    </div>
                                                    <!-- Form để upload avatar -->
                                                    <form wire:submit.prevent="updateAvatar" enctype="multipart/form-data">
                                                        <input type="file" id="avatar-input-rank-box" class="avatar-input" wire:model="new_avatar">
                                                        @error('new_avatar') <span class="text-danger small">{{ $message }}</span> @enderror
                                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Lưu</button>
                                                        <button type="button" wire:click="$set('new_avatar', null)" class="btn btn-secondary btn-sm mt-2">Hủy</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                        <!-- Menu nhỏ -->
                                        <li class="nav-item">
                                            <a class="nav-link custom-tab active" id="account-tab" data-bs-toggle="tab" href="#account" role="tab" aria-controls="account" aria-selected="true">Tài khoản của tôi</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link custom-tab" id="change-password-tab" data-bs-toggle="tab" href="#change-password" role="tab" aria-controls="change-password" aria-selected="false">Đổi mật khẩu</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link custom-tab" id="delete_account-tab" data-bs-toggle="tab" href="#delete_account" role="tab" aria-controls="delete_account" aria-selected="false">Xóa tài khoản</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link custom-tab" id="address-tab" data-bs-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false">Địa chỉ</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="tab-content dashboard-content">
                                    <!-- Tab "Tài khoản của tôi" -->
                                    <div class="tab-pane fade active show" id="account" role="tabpanel" aria-labelledby="account-tab">
                                        @livewire('customer.customer-account-component')
                                    </div>
                                    <!-- Tab "Đổi mật khẩu" -->
                                    <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                                        @livewire('customer.customer-change-password-component')
                                    </div>
                                    <!-- Tab "Xóa tài khoản" -->
                                    <div class="tab-pane fade" id="delete_account" role="tabpanel" aria-labelledby="delete_account-tab">
                                        @livewire('customer.customer-delete-account-component')
                                    </div>
                                    <!-- Tab "Địa chỉ" -->
                                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                                        @livewire('customer.customer-address-component')
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