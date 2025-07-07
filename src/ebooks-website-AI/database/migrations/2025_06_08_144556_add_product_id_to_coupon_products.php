<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToCouponProducts extends Migration
{
    public function up(): void
    {
        Schema::table('coupon_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('coupon_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('coupon_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
}