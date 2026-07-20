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
                Schema::create('project_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('project_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('level_order');
            $table->string('level_name');

            $table->boolean('is_started')->default(false);
            $table->boolean('is_completed')->default(false);

            // tidak pakai timestamps karena model $timestamps = false
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
