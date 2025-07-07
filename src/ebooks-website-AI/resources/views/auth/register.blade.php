<x-app-layout>
    <style>
        .input-box {
            width: 100%;
            display: flex;
            align-items: center;
            position: relative;
        }

        .input-box img {
            width: 18px;
            cursor: pointer;
            position: absolute;
            right: 10px;
        }

        .btn-google {
            background-color: rgb(255, 255, 255);
            color: #000000;
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            border: 1px solid #000;
            /* ✅ Viền đen */
            gap: 8px;
        }

        .btn-google:hover {
            background-color: rgb(255, 255, 255);
            color: #000;
            text-decoration: none;
        }

        .btn-google img {
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }

        .login-wrap {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            background: #f8f9fa;
            padding: 20px 0;
        }

        .breadcrumb {
            font-size: 16px;
            color: #6c757d;
        }

        .breadcrumb a {
            color: #007bff;
        }

        .login-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .login-footer .checkbox {
            font-size: 12px;
        }

        .login-footer a {
            color: #007bff;
        }
    </style>
    <main class="main">
        <!-- <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href={{ route('home') }} rel="nofollow" style="color: #F15412;">Trang Chủ</a>
                    <span></span> Đăng Ký
                </div>
            </div>
        </div> -->
        <section class="pt-30 pb-30" style="background-color: #dc3545;">

            <div class="container">
                <div class="row">

                    <div class="col-lg-10 m-auto">
                        <div class="row">
                            <div class="col-lg-3">

                            </div>
                            <div class="col-lg-6">
                                <div class="login_wrap widget-taber-content p-30 background-white border-radius-5" style="background-color:rgb(255, 255, 255);">
                                    <div class="padding_eight_all bg-white">
                                        <div class="heading_s1">
                                            <h3 class="mb-30" style="text-align: center">Đăng Ký</h3>
                                        </div>
                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf
                                            <div class="form-group">
                                                <input type="text" required="" name="name" placeholder="Vui lòng nhập họ tên!" :value="old('name')">
                                                <x-input-error :messages="$errors->get('name')" class="text-danger" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" required="" name="email" placeholder="Vui lòng nhập email!" :value="old('email')">
                                                <x-input-error :messages="$errors->get('email')" class="text-danger" />
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="password" placeholder="Phải đủ 8 ký tự">
                                            </div>
                                            <div class="form-group">
                                                <input required="" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu">
                                                <x-input-error :messages="$errors->get('password')" class="text-danger" />
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-danger w-100 btn-sm" name="login">ĐĂNG KÝ</button>
                                            </div>
                                        </form>
                                        <!-- Nút đăng nhập bằng Google -->
                                        <div class="form-group text-center">
                                            <a href="{{ route('google.redirect') }}" class="btn-google">
                                                <img src="{{ asset('assets/imgs/login/google1.png') }}" alt="Google Icon">
                                                Đăng ký bằng Google
                                            </a>
                                        </div>
                                        <div class="text-muted text-center">Đã có tài khoản?<a href="{{ route('login') }}"> Đăng nhập ngay</a></div>




                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-app-layout>