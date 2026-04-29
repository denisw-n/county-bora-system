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
        Schema::create('report_media', function (Blueprint $table) {
            // Using UUID for consistency with the rest of County Bora
            $table->uuid('id')->primary();
            
            // Link to the main report
            $table->foreignUuid('report_id')->constrained('reports')->onDelete('cascade');
            
            // Storage details
            $table->string('file_path'); 
            $table->string('file_type')->default('image'); // e.g., image, video
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_media');
    }
};