<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Xóa bảng nếu tồn tại
        Schema::dropIfExists('saletimers');

        // Tạo bảng mới
        Schema::create('saletimers', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date')->nullable(); // Thời gian bắt đầu
            $table->dateTime('sale_timer')->nullable(); // Thời gian kết thúc
            $table->boolean('status')->default(0); // Trạng thái
            $table->timestamps();
        });

       
        DB::table('saletimers')->insert([
            'id' => 1,
            'start_date' => '2025-05-15 12:00:00',
            'sale_timer' => '2025-05-31 12:00:00',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        // Xóa bảng để hoàn tác
        Schema::dropIfExists('saletimers');
    }
};
