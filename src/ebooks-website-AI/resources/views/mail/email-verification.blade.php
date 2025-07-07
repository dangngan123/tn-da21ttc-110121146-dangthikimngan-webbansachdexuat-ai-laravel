<!DOCTYPE html>
<html>

<head>
    <title>Xác thực email</title>
</head>

<body>
    <h2>Xác thực email của bạn</h2>
    <p>Chào {{ $name }},</p>
    <p>Cảm ơn bạn đã đăng ký tài khoản! Dưới đây là mã xác thực email của bạn:</p>
    <p style="font-size: 24px; font-weight: bold; color: #2260ff;">{{ $otp }}</p>
    <p>Vui lòng nhập mã này vào trang xác thực để hoàn tất đăng ký.</p>
    <p>Mã này sẽ hết hạn sau 24 giờ.</p>
    <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email.</p>
    <p>Trân trọng,<br>{{ config('app.name') }}</p>
</body>

</html>