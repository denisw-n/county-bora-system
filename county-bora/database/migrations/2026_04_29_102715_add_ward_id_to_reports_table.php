<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // We use foreignUuid because your system uses UUIDs
            // nullable() ensures your current reports don't throw an error
            $table->foreignUuid('ward_id')
                  ->after('user_id') 
                  ->nullable() 
                  ->constrained('wards')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['ward_id']);
            $table->dropColumn('ward_id');
        });
    }
};