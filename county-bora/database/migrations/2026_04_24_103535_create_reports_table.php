<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Using 'id' but as a UUID string
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            
            // Dashboard Requirements
            $table->string('title');
            $table->string('location');
            $table->string('category'); 
            $table->text('description'); 

            // Spatial Data (Nullable for testing)
            $table->decimal('latitude', 10, 8)->nullable(); 
            $table->decimal('longitude', 11, 8)->nullable();

            // Admin Management
            $table->string('status')->default('pending'); 
            $table->string('priority')->default('medium'); 
            $table->foreignUuid('dept_id')->nullable()->constrained('departments'); 

            $table->text('admin_comment')->nullable(); 
            $table->text('audit_remarks')->nullable(); 

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};