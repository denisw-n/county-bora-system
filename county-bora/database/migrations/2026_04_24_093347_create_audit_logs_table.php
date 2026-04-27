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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Using UUID for the log entry itself
            
            // FIX: Changed from foreignId to foreignUuid to match your users table
            $table->foreignUuid('admin_id')->constrained('users')->onDelete('cascade');
            
            $table->string('action'); // e.g., "Status Updated", "Priority Changed"
            
            // This links to the ID of the Report being modified.
            // Since Reports now use UUIDs, this must be a UUID field.
            $table->uuid('target_id'); 
            
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};