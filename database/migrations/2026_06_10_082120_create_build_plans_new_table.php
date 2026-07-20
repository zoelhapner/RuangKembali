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
        Schema::create('build_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('project_id');
            $table->foreignId('build_process_item_id')->nullable();
            $table->foreignId('rab_item_id')->nullable();
            $table->string('category_name')->nullable();
            $table->integer('category_order')->default(0);
            $table->string('uraian_name')->nullable();
            $table->integer('uraian_order')->default(0);
            $table->foreignId('job_category_id')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('item_order')->default(0);
            $table->double('volume')->default(0);
            $table->double('price')->default(0);
            $table->double('total')->default(0);
            $table->string('satuan')->nullable();
            $table->double('bobot_percent')->default(0);
            $table->double('planned_progress')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_plans');
    }
};
