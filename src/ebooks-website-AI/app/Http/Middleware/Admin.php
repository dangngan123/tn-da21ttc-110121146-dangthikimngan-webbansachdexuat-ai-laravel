<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (Auth::check()) {
            $user = Auth::user();

            // Kiểm tra nếu người dùng là admin
            if ($user->utype === 'admin') {
                // Chặn admin truy cập các trang giỏ hàng, thanh toán và các hành động liên quan
                if (
                    $request->routeIs('cart') ||
                    $request->routeIs('checkout') ||
                    $request->routeIs('cart.*') // Chặn các route liên quan đến giỏ hàng (thêm, xóa, v.v.)
                ) {
                    return redirect()->route('admin.dashboard')->with('error', 'Admin không được phép truy cập các chức năng mua sắm.');
                }
            } else {
                // Nếu không phải admin (tức là customer), chặn truy cập khu vực admin
                if (
                    $request->routeIs('admin.*') || // Chặn tất cả các route bắt đầu bằng 'admin.'
                    str_starts_with($request->path(), 'admin') // Chặn bất kỳ URL nào bắt đầu bằng 'admin'
                ) {
                    return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
                }
            }
        } else {
            // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        return $next($request);
    }
}
