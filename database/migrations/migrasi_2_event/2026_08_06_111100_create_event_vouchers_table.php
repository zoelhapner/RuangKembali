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
Schema::create('event_vouchers', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->foreignUuid('event_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('code')->unique();
    $table->decimal('discount', 10, 2);
    $table->enum('type', ['fixed', 'percent']);

    $table->dateTime('expired_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_vouchers');
    }
};
