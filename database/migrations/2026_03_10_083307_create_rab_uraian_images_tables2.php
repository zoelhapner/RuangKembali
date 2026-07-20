<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('rab_process_uraians', function (Blueprint $table) {
            $table->string('category_id')->nullable();
        });
    }

    public function down(): void
    {
        //
    }
};
