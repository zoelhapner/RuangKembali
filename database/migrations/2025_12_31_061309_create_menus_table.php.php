<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->string('text');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();

            $table->string('type')->default('route');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->string('permission_name')->nullable();

            $table->timestamps(0);

            // index
            $table->index('parent_id');

            // self foreign key
            $table->foreign('parent_id')
                ->references('id')
                ->on('menus')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
