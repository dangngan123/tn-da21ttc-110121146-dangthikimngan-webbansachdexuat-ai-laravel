<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
      {
          Schema::table('orders', function (Blueprint $table) {
              $table->integer('province_id')->nullable()->after('phone');
              $table->integer('district_id')->nullable()->after('province');
              $table->string('ward_code')->nullable()->after('district');
              $table->string('status')->default('ordered')->change();
          });
      }

      public function down(): void
      {
          Schema::table('orders', function (Blueprint $table) {
              $table->dropColumn(['province_id', 'district_id', 'ward_code']);
              $table->string('status')->default('odered')->change();
          });
      }
};
