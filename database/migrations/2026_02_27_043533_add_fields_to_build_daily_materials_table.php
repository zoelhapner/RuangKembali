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
        Schema::table('build_daily_materials', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('diterima');
            $table->string('ditolak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_daily_materials', function (Blueprint $table) {
            //
        });
    }
};
