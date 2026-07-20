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
        Schema::table('build_process_items', function (Blueprint $table) {
            $table->index('parent_id');
            $table->index(['project_id', 'is_tambahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_process_items', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['project_id', 'is_tambahan']);
        });
    }
};
