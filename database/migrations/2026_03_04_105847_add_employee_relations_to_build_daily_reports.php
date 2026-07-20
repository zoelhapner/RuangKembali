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
        Schema::table('build_daily_reports', function (Blueprint $table) {

            $table->foreignUuid('mk_id')
                  ->nullable()
                  ->after('mk')
                  ->constrained('employees')
                  ->nullOnDelete();

            $table->foreignUuid('kontraktor_ttd_id')
                  ->nullable()
                  ->after('kontraktor_ttd')
                  ->constrained('employees')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_daily_reports', function (Blueprint $table) {
            $table->dropForeign(['mk_id']);
            $table->dropForeign(['kontraktor_ttd_id']);
            $table->dropColumn(['mk_id','kontraktor_ttd_id']);
        });
    }
};
