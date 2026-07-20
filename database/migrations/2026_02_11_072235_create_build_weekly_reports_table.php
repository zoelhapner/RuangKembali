<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_weekly_reports', function (Blueprint $t) {
            $t->id();

            $t->foreignUuid('project_id')
              ->constrained()
              ->cascadeOnDelete();

            $t->integer('week_no');

            $t->date('start_date');
            $t->date('end_date');

            $t->text('catatan')->nullable();

            $t->foreignUuid('created_by')->nullable()
              ->constrained('users')
              ->nullOnDelete();

            $t->timestamps();

            $t->unique(['project_id','week_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_weekly_reports');
    }
};
