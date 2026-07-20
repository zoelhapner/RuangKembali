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
        Schema::create('product_color', function (Blueprint $table) {

            // product_id pakai UUID
            $table->uuid('product_id');

            // color_id pakai id biasa (bigint unsigned)
            $table->unsignedBigInteger('color_id');

            // Foreign keys
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            // $table->foreign('color_id')
            //     ->references('id')->on('colors')
            //     ->onDelete('cascade');

            $table->timestamps();

            // optional: kombinasi unik
            $table->unique(['product_id', 'color_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_color');
    }
};
