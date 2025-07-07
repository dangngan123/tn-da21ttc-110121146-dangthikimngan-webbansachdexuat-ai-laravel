<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->enum('payment_type', ['cod', 'payos', 'vnpay']); // Thêm tùy chọn 'online' cho giao dịch online
            $table->enum('status', ['pending', 'approved', 'declined']); // Trạng thái giao dịch
            $table->decimal('amount', 10, 2); // Số tiền giao dịch
            $table->string('transaction_id')->nullable(); // ID giao dịch từ cổng thanh toán
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->CascadeOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->CascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
