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
        Schema::create('chat_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên người dùng
            $table->string('email'); // Email người dùng
            $table->string('phone'); // Số điện thoại người dùng
            $table->string('support_option')->nullable(); // Loại dịch vụ hỗ trợ
            $table->string('session_key')->nullable(); // Định danh phiên trò chuyện
            $table->string('guest_token')->nullable(); // Định danh người dùng khách
            $table->text('question'); // Câu hỏi từ người dùng
            $table->text('answer'); // Câu trả lời từ bot hoặc admin
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Khóa ngoại liên kết với bảng users
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_interactions');
    }
};
