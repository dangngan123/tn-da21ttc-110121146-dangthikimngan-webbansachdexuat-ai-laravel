<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Kiểm tra email đã tồn tại trong bảng email_verifications
        if (EmailVerification::where('email', $request->email)->exists()) {
            return back()->withErrors([
                'email' => 'Email này đang chờ xác thực. Vui lòng <a href="' . route('verification.otp') . '">nhập mã OTP</a> hoặc <a href="' . route('verification.resend.form') . '">gửi lại OTP</a> nếu chưa nhận được.',
            ])->withInput();
        }

        // Tạo token và OTP xác thực
        $token = Str::random(60);
        $otp = mt_rand(100000, 999999); // Tạo mã OTP 6 chữ số
        $expiresAt = Carbon::now()->addHours(24);

        // Lưu thông tin xác thực tạm thời
        try {
            EmailVerification::create([
                'email' => $request->email,
                'token' => $token,
                'otp' => $otp,
                'password' => Hash::make($request->password),
                'name' => $request->name,
                'phone' => $request->phone,
                'expires_at' => $expiresAt,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create email verification record: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Đã có lỗi xảy ra khi lưu thông tin xác thực. Vui lòng thử lại.']);
        }

        // Gửi email xác thực với OTP
        try {
            Mail::to($request->email)->send(new EmailVerificationMail($otp, $request->name, $request->email));
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi email xác thực. Vui lòng thử lại sau.']);
        }

        // Chuyển hướng người dùng đến trang nhập OTP
        return redirect()->route('verification.otp')
            ->with('success', 'Mã OTP đã được gửi đến ' . $request->email . '. Vui lòng kiểm tra hộp thư (hoặc thư rác).')
            ->with('email', $request->email);
    }

    public function showOtpForm(Request $request)
    {
        $email = $request->query('email', session('email'));
        if (!$email || !EmailVerification::where('email', $email)->exists()) {
            return redirect()->route('register')
                ->with('error', 'Không tìm thấy thông tin xác thực. Vui lòng đăng ký lại hoặc <a href="' . route('verification.resend.form') . '">gửi lại OTP</a>.');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $verification = EmailVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$verification) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng. Vui lòng kiểm tra lại.']);
        }

        if (Carbon::now()->gt($verification->expires_at)) {
            $verification->delete();
            return redirect()->route('login')
                ->with('error', 'Mã OTP đã hết hạn. Vui lòng đăng ký lại để nhận mã mới.');
        }

        // Kiểm tra email đã được sử dụng chưa
        $existingUser = User::where('email', $verification->email)->first();
        if ($existingUser) {
            if ($existingUser->status == 0) {
                Log::warning('Blocked user attempted to verify OTP', [
                    'email' => $existingUser->email,
                    'user_id' => $existingUser->id,
                ]);
                $verification->delete();
                return redirect()->route('login')
                    ->with('error', 'Tài khoản của bạn đã bị khóa vì lý do vi phạm chính sách hoặc hành vi không phù hợp. Vui lòng liên hệ với bộ phận hỗ trợ để được giải quyết.');
            }
            $verification->delete();
            return redirect()->route('login')
                ->with('error', 'Email này đã được đăng ký. Vui lòng <a href="' . route('login') . '">đăng nhập</a>.');
        }

        // Tạo người dùng mới
        try {
            $user = User::create([
                'name' => $verification->name,
                'email' => $verification->email,
                'phone' => $verification->phone,
                'password' => $verification->password,
                'email_verified_at' => Carbon::now(),
                'utype' => 'customer',
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage(), [
                'email' => $verification->email,
                'data' => [
                    'name' => $verification->name,
                    'email' => $verification->email,
                    'phone' => $verification->phone,
                    'utype' => 'customer',
                    'status' => 1,
                ],
            ]);
            return redirect()->route('login')
                ->with('error', 'Đã có lỗi khi tạo tài khoản. Vui lòng thử lại sau.');
        }

        // Xóa bản ghi xác thực
        $verification->delete();

        event(new Registered($user));

        // Đăng nhập người dùng
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Đăng ký thành công! Chào mừng bạn đến với hệ thống.');
    }

    public function showResendOtpForm()
    {
        return view('auth.resend-otp');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        // Kiểm tra email trong bảng email_verifications
        $verification = EmailVerification::where('email', $request->email)->first();

        if (!$verification) {
            return redirect()->route('register')
                ->with('error', 'Email không tồn tại hoặc đã được xác thực. Vui lòng đăng ký lại.');
        }

        // Kiểm tra email đã được sử dụng trong bảng users
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            if ($existingUser->status == 0) {
                Log::warning('Blocked user attempted to resend OTP', [
                    'email' => $existingUser->email,
                    'user_id' => $existingUser->id,
                ]);
                $verification->delete();
                return redirect()->route('login')
                    ->with('error', 'Tài khoản của bạn đã bị khóa vì lý do vi phạm chính sách hoặc hành vi không phù hợp. Vui lòng liên hệ với bộ phận hỗ trợ để được giải quyết.');
            }
            return redirect()->route('login')
                ->with('error', 'Email này đã được đăng ký. Vui lòng <a href="' . route('login') . '">đăng nhập</a>.');
        }

        // Xóa OTP cũ và tạo OTP mới
        $verification->delete();

        $newOtp = mt_rand(100000, 999999);
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addHours(24);

        try {
            EmailVerification::create([
                'email' => $request->email,
                'token' => $token,
                'otp' => $newOtp,
                'password' => $verification->password,
                'name' => $verification->name,
                'phone' => $verification->phone,
                'expires_at' => $expiresAt,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create new email verification record: ' . $e->getMessage());
            return redirect()->route('verification.otp')
                ->with('error', 'Đã có lỗi khi tạo mã OTP mới. Vui lòng thử lại.')
                ->with('email', $request->email);
        }

        // Gửi email với OTP mới
        try {
            Mail::to($request->email)->send(new EmailVerificationMail($newOtp, $verification->name, $request->email));
        } catch (\Exception $e) {
            Log::error('Failed to resend email verification: ' . $e->getMessage());
            return redirect()->route('verification.otp')
                ->with('error', 'Không thể gửi email xác thực. Vui lòng thử lại sau.')
                ->with('email', $request->email);
        }

        return redirect()->route('verification.otp')
            ->with('success', 'Mã OTP mới đã được gửi đến ' . $request->email . '. Vui lòng kiểm tra hộp thư (hoặc thư rác).')
            ->with('email', $request->email);
    }
}
