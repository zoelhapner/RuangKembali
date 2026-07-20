<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('build_progress_snapshots', function (Blueprint $t) {
            $t->id();

            $t->foreignUuid('project_id')
              ->constrained()
              ->cascadeOnDelete();

            $t->integer('week_no');

            $t->decimal('progress_weighted', 6,2);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('build_progress_snapshots');
    }
};
