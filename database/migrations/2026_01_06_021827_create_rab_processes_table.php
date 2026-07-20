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
Schema::create('rab_process', function (Blueprint $table) {
    $table->id();
    $table->uuid('project_id');

    $table->string('contact_name');
    $table->string('job_location');
    $table->string('job_duration');

    $table->decimal('subtotal', 15, 2)->default(0);
    $table->decimal('discount', 15, 2)->default(0);
    $table->decimal('subtotal_after_discount', 15, 2)->default(0);

    $table->decimal('tax_rate', 5, 2)->default(0);
    $table->decimal('tax_total', 15, 2)->default(0);

    $table->decimal('shipping', 15, 2)->default(0);
    $table->decimal('grand_total', 15, 2)->default(0);

    $table->text('notes')->nullable();

    $table->timestamps();

    $table->foreign('project_id')
        ->references('id')
        ->on('projects')
        ->cascadeOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_process');
    }
};
