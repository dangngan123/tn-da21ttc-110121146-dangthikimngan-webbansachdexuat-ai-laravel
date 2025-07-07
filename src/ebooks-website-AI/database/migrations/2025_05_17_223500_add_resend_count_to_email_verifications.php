<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('email_verifications', function (Blueprint $table) {
            $table->unsignedInteger('resend_count')->default(0);
            $table->timestamp('last_resend_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('email_verifications', function (Blueprint $table) {
            $table->dropColumn(['resend_count', 'last_resend_at']);
        });
    }
};
