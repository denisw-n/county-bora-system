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
            // Changed from 'department_id' to 'dept_id' to align with your project standard
            $table->uuid('dept_id')->after('id')->nullable();
            
            // If you want to add the foreign key constraint:
            // $table->foreign('dept_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            $table->dropColumn('dept_id');
        });
    }
};