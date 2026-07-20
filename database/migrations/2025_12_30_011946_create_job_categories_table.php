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
        Schema::create('job_categories', function (Blueprint $table) {
    $table->id();
    $table->string('bidang')->nullable();
    $table->string('kode_group')->nullable();
    $table->string('nama_group')->nullable();
    $table->string('kode');
    $table->string('kode_urut');
    $table->string('nama_pekerjaan');
    $table->string('satuan');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_categories');
    }
};
