<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This is empty because we manually created the table in phpMyAdmin.
     * Running this will simply record the migration as "Finished" in the system.
     */
    public function up(): void
    {
        // No code here - table already exists and is working.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if you want to wipe it completely
        // Schema::dropIfExists('personal_access_tokens');
    }
};