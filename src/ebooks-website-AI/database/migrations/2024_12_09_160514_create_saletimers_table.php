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
        Schema::create('saletimers', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sale_timer'); // Thời gian giảm giá
            $table->boolean('status'); // Trạng thái giảm giá (1: đang giảm giá, 0: không giảm giá)
            $table->dateTime('end_date')->nullable(); // Ngày kết thúc giảm giá
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saletimers');
    }
};
