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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('subtotal');
            $table->decimal('discount')->default(0); // Giảm giá
            $table->decimal('shipping_cost'); // Phí vận chuyển
            $table->decimal('total'); // Tổng tiền
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->string('address');
            $table->string('additional_info')->nullable();
            $table->string('status')->default('odered');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
