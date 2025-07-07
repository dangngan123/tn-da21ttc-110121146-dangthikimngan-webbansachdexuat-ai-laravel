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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // Khóa chính
            $table->unsignedBigInteger('user_id'); // Khóa ngoại tham chiếu tới bảng users
            $table->unsignedBigInteger('order_item_id'); // Khóa ngoại tham chiếu tới bảng order_items
          

            $table->integer('rating'); // Đánh giá
            $table->text('comment'); // Nội dung đánh giá
            $table->string('images')->nullable(); // Tên file ảnh, có thể để trống
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Trạng thái
            $table->timestamps(); // Cột created_at và updated_at

            // Thiết lập khóa ngoại
            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
