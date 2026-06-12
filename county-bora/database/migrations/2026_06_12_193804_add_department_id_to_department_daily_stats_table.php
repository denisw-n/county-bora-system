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
            // Adding the foreign key column to link to departments table
            // We use uuid() to match the primary key type of your Departments model
            $table->uuid('department_id')->after('id')->nullable();
            
            // Optional: Add a foreign key constraint for data integrity
            // $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_daily_stats', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
};