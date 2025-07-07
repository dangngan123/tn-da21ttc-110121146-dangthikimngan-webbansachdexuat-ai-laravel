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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_code')->unique();
            $table->enum('coupon_type', ['fixed', 'percent']);
            $table->decimal('coupon_value', 10, 2); // Giá trị giảm giá
            $table->decimal('cart_value', 10, 2); // Giá trị đơn hàng tối thiểu để áp dụng mã
            $table->date('start_date')->nullable();
            $table->date('end_date');
            $table->unsignedInteger('max_uses')->nullable(); // Số lượng sử dụng tối đa
            $table->unsignedInteger('used')->default(0); // Số lượng đã sử dụng
            $table->boolean('is_active')->default(1); // Trạng thái hoạt động
            $table->text('description')->nullable(); // Mô tả
            $table->unsignedBigInteger('user_id')->nullable(); // Giảm giá riêng cho người dùng
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
