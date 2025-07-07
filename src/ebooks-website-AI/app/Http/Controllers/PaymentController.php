<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use App\Mail\OrderConfirmedMail;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        Log::info('PayOS Success Callback', [
            'request_data' => $request->all(),
        ]);

        $transaction = Transaction::where('order_id', $request->orderCode)->first();
        $order = Order::find($request->orderCode);

        if ($transaction && $order && $request->status === 'PAID') {
            $transaction->status = 'approved';
            $transaction->transaction_id = $request->id; // ID giao dịch từ PayOS
            $transaction->save();

            $order->status = 'paid';
            $order->save();

            // Gửi email xác nhận
            Mail::to($order->email)->send(new OrderConfirmedMail($order));

            // Xóa giỏ hàng và session
            Cart::instance('cart')->destroy();
            session()->forget('checkout');

            Log::info('Payment Success', [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
            ]);

            return redirect()->route('thankyou')->with('message', 'Thanh toán thành công!');
        }

        Log::warning('Payment Success Failed', [
            'order_id' => $request->orderCode,
            'transaction_found' => $transaction ? true : false,
            'order_found' => $order ? true : false,
            'status' => $request->status,
        ]);

        return redirect()->route('home')->with('error', 'Thanh toán không thành công. Vui lòng liên hệ hỗ trợ.');
    }

    public function cancel(Request $request)
    {
        Log::info('PayOS Cancel Callback', [
            'request_data' => $request->all(),
        ]);

        $transaction = Transaction::where('order_id', $request->orderCode)->first();
        $order = Order::find($request->orderCode);

        if ($transaction && $order) {
            $transaction->status = 'declined';
            $transaction->save();

            $order->status = 'canceled';
            $order->save();

            Log::info('Payment Canceled', [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
            ]);
        }

        return redirect()->route('home')->with('error', 'Thanh toán đã bị hủy.');
    }
}
