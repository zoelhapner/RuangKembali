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
        Schema::create('build_plan_weeks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('build_plan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('week_no');

            $table->double('plan_percent')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'build_plan_id',
                'week_no'
            ]);
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
