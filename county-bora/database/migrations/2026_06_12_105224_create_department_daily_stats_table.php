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
        Schema::create('department_daily_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dept_id')->constrained('departments');
            $table->foreignUuid('ward_id')->constrained('wards');
            $table->date('date');
            $table->integer('resolved_count');
            $table->integer('pending_count');
            $table->timestamps();

            // Prevents duplicate entries for the same group on the same day
            $table->unique(['dept_id', 'ward_id', 'date'], 'dept_ward_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_daily_stats');
    }
};