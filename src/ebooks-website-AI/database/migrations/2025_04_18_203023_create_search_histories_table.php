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
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Cột khóa ngoại đến bảng users
            $table->string('keyword'); // Từ khóa tìm kiếm
            $table->timestamp('searched_at'); // Thời điểm tìm kiếm
            $table->timestamps();
            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Xóa lịch sử tìm kiếm khi xóa người dùng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_histories');
    }
};
