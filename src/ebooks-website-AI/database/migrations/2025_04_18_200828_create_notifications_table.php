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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // người nhanh thông báo
            $table->string('title'); // tiêu đề
            $table->text('message'); // nội dung
            $table->boolean('is_read')->default(false); // trạng thái đã đọc
            $table->string('type')->nullable(); // loại thông báo: order, promo, system, etc
            $table->timestamp('read_at')->nullable(); // thời điểm đọc

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // xóa thông báo khi xóa người dùng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
