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
        Schema::create('warehouses', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name');
        $table->string('responsible_person');
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->longText('address');

        $table->unsignedBigInteger('province_id')->nullable();
        $table->foreign('province_id')
            ->references('id')->on('provinces')
            ->nullOnDelete();

        $table->unsignedBigInteger('city_id')->nullable();
        $table->foreign('city_id')
            ->references('id')->on('cities')
            ->nullOnDelete();

        $table->unsignedBigInteger('district_id')->nullable();
        $table->foreign('district_id')
            ->references('id')->on('districts')
            ->nullOnDelete();

        $table->unsignedBigInteger('sub_district_id')->nullable();
        $table->foreign('sub_district_id')
            ->references('id')->on('sub_districts')
            ->nullOnDelete();

        $table->unsignedBigInteger('postal_code_id')->nullable();
        $table->foreign('postal_code_id')
            ->references('id')->on('postal_codes')
            ->nullOnDelete();

        $table->string('description')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
