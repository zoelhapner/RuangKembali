<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_daily_reports', function (Blueprint $t) {
            $t->id();

            $t->foreignUuid('project_id')
              ->constrained()
              ->cascadeOnDelete();

            $t->date('tanggal');

            $t->string('cuaca')->nullable();

            $t->time('jam_mulai')->nullable();
            $t->time('jam_selesai')->nullable();

            $t->text('pekerjaan')->nullable();
            $t->text('catatan')->nullable();

            $t->foreignUuid('created_by')->nullable()
              ->constrained('users')
              ->nullOnDelete();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_daily_reports');
    }
};
