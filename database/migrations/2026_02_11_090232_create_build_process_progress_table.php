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
Schema::create('build_process_progress', function (Blueprint $table) {
    $table->id();

    $table->foreignId('build_process_item_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->foreignId('weekly_report_id')
          ->nullable()
          ->constrained('build_weekly_reports')
          ->nullOnDelete();

    $table->integer('week_no');

    // progress minggu ini (incremental)
    $table->decimal('progress_percent', 5,2)->default(0);

    $table->text('note')->nullable();

    $table->timestamps();

    $table->unique(['build_process_item_id','week_no']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_process_progress');
    }
};
