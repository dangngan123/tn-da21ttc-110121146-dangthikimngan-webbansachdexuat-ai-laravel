<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_verifications', function (Blueprint $table) {
            $table->string('password')->after('otp'); // Thêm cột password
            $table->string('name')->after('password'); // Thêm cột name
            $table->string('phone')->nullable()->after('name'); // Thêm cột phone
        });
    }

    public function down(): void
    {
        Schema::table('email_verifications', function (Blueprint $table) {
            $table->dropColumn(['password', 'name', 'phone']);
        });
    }
};
