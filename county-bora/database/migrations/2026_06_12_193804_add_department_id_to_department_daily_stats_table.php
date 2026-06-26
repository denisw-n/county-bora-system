<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('department_daily_stats', 'dept_id')) {
            Schema::table('department_daily_stats', function (Blueprint $table) {
                $table->foreignUuid('dept_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('departments')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('department_daily_stats', 'dept_id')) {
            Schema::table('department_daily_stats', function (Blueprint $table) {
                $table->dropForeign(['dept_id']);
                $table->dropColumn('dept_id');
            });
        }
    }
};