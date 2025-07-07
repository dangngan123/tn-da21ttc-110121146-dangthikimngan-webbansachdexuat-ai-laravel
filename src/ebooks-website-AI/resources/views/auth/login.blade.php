<x-app-layout>
    <main class="main">
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
                text-decoration: none;
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
        <!-- <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href={{ route('home') }} rel="nofollow" style="color: #F15412;">Trang Chủ</a>
                    <span></span> Đăng Nhập
                </div>
            </div>
        </div> -->
        <section class="pt-10 pb-20" style="background-color: #C12530;">

            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        <div class="row">
                            <div class="col-lg-3">

                            </div>
                            <div class="col-lg-1"></div>
                            <div class="col-lg-5">
                                <div class="login-wrap">
                                    <div class="heading_s1 text-center">
                                        <h3 class="mb-30">Đăng Nhập</h3>
                                    </div>
                                    <x-auth-session-status class="mb-4" :status="session('status')" />
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email" required placeholder="Vui lòng nhập email!" name="email" value="{{ old('email') }}" class="form-control">
                                        </div>
                                        <div class="form-group input-box">
                                            <input required="" placeholder="Vui lòng nhập mật khẩu!" type="password" name="password" id="password" class="form-control">
                                            <img class="ms-2" src="{{ asset('assets/imgs/login/close.png') }}" alt="" id="eyeicon">
                                        </div>

                                        <div class="login-footer">
                                            <div class="checkbox">
                                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox1" value="">
                                                <label class="form-check-label" for="exampleCheckbox1"><span>Nhớ mật khẩu</span></label>
                                            </div>
                                            <a href="{{ route('password.request') }}" class="text-muted">Quên mật khẩu?</a>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-danger w-100 btn-sm">ĐĂNG NHẬP</button>
                                        </div>
                                    </form>

                                    <!-- Nút đăng nhập bằng Google -->
                                    <div class="form-group text-center">
                                        <a href="{{ route('google.redirect') }}" class="btn-google">
                                            <img src="{{ asset('assets/imgs/login/google1.png') }}" alt="Google Icon">
                                            Đăng nhập bằng Google
                                        </a>
                                    </div>
                                    <div class="text-muted text-center">Chưa có tài khoản<a href="{{ route('register') }}"> Đăng ký ngay</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        let password = document.getElementById('password');
        let eyeicon = document.getElementById('eyeicon');
        eyeicon.onclick = function() {
            if (password.type == "password") {
                password.type = "text";
                eyeicon.src = "{{asset('assets/imgs/login/show.png')}}";
            } else {
                password.type = "password";
                eyeicon.src = "{{asset('assets/imgs/login/close.png')}}";
            }
        }
    </script>
</x-app-layout>