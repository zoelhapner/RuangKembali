<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_weekly_progress', function (Blueprint $t) {
            $t->id();

            $t->foreignId('weekly_report_id')
              ->constrained('build_weekly_reports')
              ->cascadeOnDelete();

            $t->foreignId('build_process_item_id')
              ->constrained('build_process_items')
              ->cascadeOnDelete();

            // progress minggu ini (bukan kumulatif)
            $t->decimal('progress_percent', 5, 2);

            $t->timestamps();

            $t->unique([
                'weekly_report_id',
                'build_process_item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_weekly_progress');
    }
};
