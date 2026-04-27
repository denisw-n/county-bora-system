<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotlines', function (Blueprint $table) {
            $table->id(); // [cite: 54]
            $table->string('service_name'); // e.g., "Fire Department" [cite: 55]
            $table->string('phone_number'); // The emergency number [cite: 56]
            $table->boolean('is_active')->default(true); // Operational status [cite: 57]
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotlines');
    }
};