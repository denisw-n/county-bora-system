<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            $table->integer('resolved_count')->default(0)->change();
            $table->integer('pending_count')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            $table->integer('resolved_count')->default(null)->change();
            $table->integer('pending_count')->default(null)->change();
        });
    }
};