<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('subtotal_after_discount', 15, 2)->default(0); // optional
        });
    }

    public function down(): void
    {
        // Schema::dropIfExists('offer_builds');
        // Schema::dropIfExists('offer_item_builds');
    }
};
