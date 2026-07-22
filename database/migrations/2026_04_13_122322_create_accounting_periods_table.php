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
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->year('year');

            $table->date('start_date');
            $table->date('end_date');

            $table->boolean('is_closed')->default(false);

            $table->timestamp('closed_at')->nullable();
            $table->uuid('closed_by')->nullable();
            $table->uuid('license_id')->nullable();
            $table->timestamps();

            // optional tapi recommended
            $table->unique(['year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
