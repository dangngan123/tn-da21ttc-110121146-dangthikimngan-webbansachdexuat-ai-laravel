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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Thêm trường user_id
            $table->unsignedBigInteger('product_id'); // Thêm trường product_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Khóa ngoại với bảng users
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // Khóa ngoại với bảng products
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
