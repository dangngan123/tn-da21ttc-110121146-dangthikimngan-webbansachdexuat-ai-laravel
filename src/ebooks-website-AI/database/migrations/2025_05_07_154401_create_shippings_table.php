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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('address_type'); // Nhà riêng, văn phòng, v.v.
            $table->string('name'); // Tên người nhận
            $table->string('phone'); // Số điện thoại
            $table->integer('province_id')->nullable(); // ID tỉnh từ GHN
            $table->string('province'); // Tên tỉnh
            $table->integer('district_id')->nullable(); // ID quận từ GHN
            $table->string('district'); // Tên quận
            $table->string('ward_code')->nullable(); // Code phường từ GHN
            $table->string('ward'); // Tên phường
            $table->string('address'); // Địa chỉ chi tiết
            $table->boolean('status')->default(1); // Trạng thái hoặc mặc định
             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
