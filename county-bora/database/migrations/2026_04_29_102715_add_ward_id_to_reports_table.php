<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add column if it does NOT already exist (prevents crash + keeps data safe)
        if (!Schema::hasColumn('reports', 'ward_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreignUuid('ward_id')
                    ->after('user_id')
                    ->nullable()
                    ->constrained('wards')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Safe rollback (only if column exists)
        if (Schema::hasColumn('reports', 'ward_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropForeign(['ward_id']);
                $table->dropColumn('ward_id');
            });
        }
    }
};