<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderCodeToOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thêm cột order_code, không áp dụng unique ngay
            $table->string('order_code')->nullable()->after('id');
        });

        // Gán giá trị cho các bản ghi hiện có
        $orders = \App\Models\Order::all();
        foreach ($orders as $order) {
            $order->order_code = $this->generateOrderCode($order->id);
            $order->save();
        }

        // Sau khi gán giá trị, thêm ràng buộc unique
        Schema::table('orders', function (Blueprint $table) {
            $table->unique('order_code', 'orders_order_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Xóa ràng buộc unique trước
            $table->dropUnique('orders_order_code_unique');
            // Xóa cột order_code
            $table->dropColumn('order_code');
        });
    }

    private function generateOrderCode($id): string
    {
        // Tạo mã đơn hàng dựa trên ngày tháng và id
        $date = now()->format('Ymd'); // Định dạng: YYYYMMDD
        return "ORD-{$date}-{$id}";
    }
}
