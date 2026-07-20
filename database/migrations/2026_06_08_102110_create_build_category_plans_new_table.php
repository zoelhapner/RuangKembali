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
        Schema::create('build_category_plans', function (Blueprint $table) { $table->id(); $table->uuid('project_id'); $table->unsignedBigInteger('category_order'); $table->integer('week_no'); $table->decimal('bobot_percent', 8, 3)->default(0); $table->timestamps(); $table->unique([ 'project_id', 'category_order', 'week_no' ]); $table->foreign('project_id') ->references('id') ->on('projects') ->onDelete('cascade'); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_category_plans');
    }
};
