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
        // Drop if exists to ensure a clean start
        Schema::dropIfExists('alerts');

        Schema::create('alerts', function (Blueprint $table) {
            // 1. Primary Key for the alert record itself (Standard BigInt)
            $table->id(); 
            
            // 2. Alert Details
            $table->string('title'); 
            $table->text('content'); 
            
            // 3. Classification: 'Emergency', 'Maintenance', or 'General'
            $table->string('type'); 
            
            // 4. Admin Relationship
            // We use foreignUuid because your Users table uses UUIDs for IDs.
            // This ensures the data types match perfectly.
            $table->foreignUuid('author_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 

            // 5. Timestamps
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};