<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            // Add the column. Use 'double' or 'decimal' for percentages.
            $table->decimal('percentage', 5, 2)->default(0.00)->after('status_breakdown');
        });
    }

    public function down(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};