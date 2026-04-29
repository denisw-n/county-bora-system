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
        Schema::create('reports', function (Blueprint $table) {
            // Identifiers
            $table->uuid('id')->primary(); 
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // The Citizen
            $table->foreignUuid('ward_id')->constrained('wards'); // For localized admin filtering

            // Citizen Inputs
            $table->string('title');
            $table->string('location'); // Descriptive location (e.g., "Near Roysambu Primary")
            $table->string('category'); // e.g., Water, Roads, Waste
            $table->text('description'); 

            // Spatial Data (GPS)
            $table->decimal('latitude', 10, 8)->nullable(); 
            $table->decimal('longitude', 11, 8)->nullable();

            // Admin Management & Workflow
            $table->string('status')->default('pending'); // pending, dispatched, in_progress, resolved, rejected
            $table->string('priority')->default('medium'); // low, medium, high, emergency
            $table->foreignUuid('dept_id')->nullable()->constrained('departments'); // Assigned Department
            
            // Admin Feedback & Oversight
            $table->text('admin_comment')->nullable(); // Visible to Citizen
            $table->text('audit_remarks')->nullable(); // Internal Admin only

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};