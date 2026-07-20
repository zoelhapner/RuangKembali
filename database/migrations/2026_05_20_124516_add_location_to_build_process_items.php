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
        Schema::table('build_process_items', function (Blueprint $table) {
            $table->integer('category_order')->nullable();
            $table->integer('uraian_order')->nullable();
            $table->integer('item_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_process_items', function (Blueprint $table) {
            //
        });
    }
};
