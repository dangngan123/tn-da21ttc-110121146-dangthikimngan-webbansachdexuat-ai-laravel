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
        // Tạo bảng user_interaction
        Schema::create('user_interaction', function (Blueprint $table) {
            $table->id('interaction_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id'); // Tương ứng với product_id
            $table->string('interaction_type', 20); // 'view', 'click', 'search', 'add_to_cart', 'order'
            $table->float('interaction_value'); // Giá trị tương tác
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interaction');
    }
};
