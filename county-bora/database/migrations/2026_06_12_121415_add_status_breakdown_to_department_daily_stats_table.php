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
        Schema::table('department_daily_stats', function (Blueprint $table) {
            // Add the dynamic JSON column
            $table->json('status_breakdown')->nullable();

            // Optional: Drop the old columns if you are no longer using them 
            // anywhere else in your application.
            $table->dropColumn(['resolved_count', 'pending_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            // Restore old columns if you need to rollback
            $table->integer('resolved_count')->default(0);
            $table->integer('pending_count')->default(0);
            
            // Drop the JSON column
            $table->dropColumn('status_breakdown');
        });
    }
};