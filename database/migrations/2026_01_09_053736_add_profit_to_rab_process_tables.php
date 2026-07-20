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
        Schema::table('rab_process', function (Blueprint $table) {
            $table->decimal('overhead_value', 15, 2)->default(0);
            $table->decimal('profit_value', 15, 2)->default(0);
            $table->decimal('overhead_percent', 15, 2)->default(0);
            $table->decimal('profit_percent', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rab_process', function (Blueprint $table) {
            //
        });
    }
};
