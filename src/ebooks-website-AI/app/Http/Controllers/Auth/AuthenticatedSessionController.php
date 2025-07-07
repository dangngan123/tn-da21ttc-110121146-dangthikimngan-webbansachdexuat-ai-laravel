<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmailVerification;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Kiểm tra xem email có đang chờ xác thực không
        if (EmailVerification::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Email này đang chờ xác thực. Vui lòng kiểm tra email để xác thực tài khoản của bạn.']);
        }

        // Kiểm tra thông tin đăng nhập
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // Kiểm tra trạng thái tài khoản
            if (!Auth::user()->status) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản của bạn đã bị vô hiệu hóa.']);
            }

            return redirect()->intended('customer/dashboard/');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không khớp với dữ liệu của chúng tôi.',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
