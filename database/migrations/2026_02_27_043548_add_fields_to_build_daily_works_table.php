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
        Schema::create('build_daily_works', function (Blueprint $table) {
            $table->id();
            $table->id('build_daily_report_id');
            $table->foreign('build_daily_report_id')->references('id')->on('build_daily_reports')->cascadeOnDelete();
            $table->uuid('rab_process_item_id')->nullable();
            $table->foreign('rab_process_item_id')->references('id')->on('rab_process_items')->cascadeOnDelete();
            $table->decimal('volume');
            $table->string('satuan');
            $table->string('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_daily_works', function (Blueprint $table) {
            //
        });
    }
};
