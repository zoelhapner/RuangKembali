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
        Schema::table('invoice_counters', function (Blueprint $table) {
            $table->string('year', 4)->after('prefix');
        });

        Schema::table('invoice_counters', function (Blueprint $table) {
            $table->dropPrimary();
            $table->primary(['prefix', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_counters', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('year');
            $table->primary('prefix');
        });
    }
};
