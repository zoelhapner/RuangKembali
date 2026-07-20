<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_item', function (Blueprint $table) {
            $table->string('category_name')->nullable();
            $table->string('uraian_name')->nullable();
            $table->decimal('volume', 12, 2)->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Schema::dropIfExists('offer_builds');
        // Schema::dropIfExists('offer_item_builds');
    }
};
