<!DOCTYPE html>
<html>

<head>
    <title>Xác thực OTP</title>
    <style>
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .text-center {
            text-align: center;
        }

        .text-danger {
            color: #dc3545;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .underline {
            text-decoration: underline;
        }

        .text-sm {
            font-size: 14px;
        }

        .text-gray-600 {
            color: #6c757d;
        }

        .hover\:text-gray-900:hover {
            color: #212529;
        }

        .ms-3 {
            margin-left: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center">Xác thực OTP</h2>
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Vui lòng nhập mã OTP đã được gửi đến email của bạn để xác thực tài khoản.') }}
                </div>

                <form method="POST" action="{{ route('verification.verify-otp') }}">
                    @csrf

                    <input type="hidden" name="email" value="{{ session('email') }}">

                    <div class="mb-3">
                        <label for="otp" class="form-label">{{ __('Mã OTP') }}</label>
                        <input id="otp" class="form-control" type="text" name="otp" required />
                        @if ($errors->has('otp'))
                        <span class="text-danger small">{{ $errors->first('otp') }}</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end">
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                            {{ __('Đã xác thực? Đăng nhập') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-3">{{ __('Xác thực') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>