<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Kiểm tra email đã tồn tại
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Kiểm tra trạng thái người dùng
                if ($existingUser->status == 0) {
                    return redirect()->route('login')
                        ->with('error', 'Tài khoản của bạn đã bị khóa vì lý do vi phạm chính sách hoặc hành vi không phù hợp. Vui lòng liên hệ với bộ phận hỗ trợ để được giải quyết.');
                }

                if ($existingUser->google_id) {
                    // Đã có tài khoản Google, chỉ cập nhật avatar nếu chưa có avatar tùy chỉnh
                    if (!$existingUser->avatar) {
                        $existingUser->update([
                            'avatar' => $googleUser->getAvatar(),
                        ]);
                    }
                    Auth::login($existingUser);
                    return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
                } else {
                    // Liên kết tài khoản hiện tại với Google, chỉ cập nhật avatar nếu chưa có
                    if (!$existingUser->avatar) {
                        $existingUser->update([
                            'google_id' => $googleUser->getId(),
                            'avatar' => $googleUser->getAvatar(),
                        ]);
                    } else {
                        $existingUser->update([
                            'google_id' => $googleUser->getId(),
                        ]);
                    }
                    Auth::login($existingUser);
                    return redirect()->route('home')
                        ->with('success', 'Tài khoản của bạn đã được liên kết với Google!');
                }
            }

            // Tạo tài khoản mới nếu chưa tồn tại, để password là NULL
            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'utype' => 'customer',
                'status' => 1,
                'password' => null, // Đảm bảo password là NULL
            ]);

            Auth::login($newUser);

            // Chuyển hướng đến trang chính
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->with('error', 'Đăng nhập thất bại. Lỗi: ' . $e->getMessage());
        }
    }
}
