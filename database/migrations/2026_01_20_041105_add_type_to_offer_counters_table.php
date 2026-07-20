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
        Schema::table('offer_counters', function (Blueprint $table) {
            $table->string('type', 10)->after('id')->default('DSN');

            // Hapus unique lama
            $table->dropUnique(['year']);

            // Buat unique baru
            $table->unique(['type', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_counters', function (Blueprint $table) {
            $table->dropUnique(['type', 'year']);
            $table->unique('year');
            $table->dropColumn('type');
        });
    }
};
