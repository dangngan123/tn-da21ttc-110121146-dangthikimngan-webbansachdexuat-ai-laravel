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
        Schema::create('products', function (Blueprint $table) {
            //Thông tin cơ bản
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('short_description');
            $table->longText('long_description')->nullable();
            //Giá cả
            $table->decimal('reguler_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            //Quản lý kho
            $table->unsignedInteger('quantity')->default(10);
            //Hình ảnhảnh
            $table->string('image');
            $table->string('images')->nullable();
            //Thông tin sách
            $table->string('publisher')->nullable(); // Nhà xuất bản
            $table->string('author')->nullable(); // Tác giả
            // $table->year('published_year')->nullable(); // Năm xuất bản
            // $table->unsignedInteger('pages')->nullable(); // Số trang
            $table->string('age')->nullable(); // Độ tuổi
            //Thông tin khác
            // $table->unsignedBigInteger('view_count')->default(0); // Lượt xem
            // $table->unsignedBigInteger('sold_count')->default(0); // Lượt bán
            //Liên kết với danh mục
            $table->unsignedBigInteger('category_id'); // Chỉnh sửa kiểu dữ liệu
            $table->boolean('is_hot')->default(0);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
