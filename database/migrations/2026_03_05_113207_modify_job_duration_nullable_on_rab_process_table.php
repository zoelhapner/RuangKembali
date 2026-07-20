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
            $table->string('job_duration')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rab_process', function (Blueprint $table) {
            $table->string('job_duration')->nullable(false)->change();
        });
    }
};
