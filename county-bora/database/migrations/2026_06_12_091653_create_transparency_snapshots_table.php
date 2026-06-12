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
        Schema::create('transparency_snapshots', function (Blueprint $table) {
            // Using UUID to stay consistent with your other tables
            $table->uuid('id')->primary(); 
            
            // Defines what kind of stat this is (e.g., 'department_performance')
            $table->string('metric_type'); 
            
            // The ID of the specific area (Dept or Ward) being tracked
            $table->string('entity_id');   
            
            // The data columns
            $table->integer('total_count');
            $table->integer('resolved_count');
            $table->decimal('percentage', 5, 2);
            
            // To track when this specific statistic was captured
            $table->timestamp('snapshot_date'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transparency_snapshots');
    }
};