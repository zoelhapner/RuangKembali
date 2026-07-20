<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('nickname')->nullable()->change();
            $table->unsignedTinyInteger('gender')->nullable()->change();

            $table->string('birth_place')->nullable()->change();
            $table->string('identity_number')->nullable()->change();
            $table->string('birth_date', 10)->nullable()->change();

            $table->unsignedBigInteger('religion_id')->nullable()->change();

            $table->longText('address')->nullable()->change();

            $table->unsignedBigInteger('province_id')->nullable()->change();
            $table->unsignedBigInteger('city_id')->nullable()->change();
            $table->unsignedBigInteger('district_id')->nullable()->change();
            $table->unsignedBigInteger('sub_district_id')->nullable()->change();
            $table->unsignedBigInteger('postal_code_id')->nullable()->change();

            $table->string('photo')->nullable()->change();
            $table->string('identity_photo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('nickname')->nullable(false)->change();
            $table->unsignedTinyInteger('gender')->nullable(false)->change();

            $table->string('birth_place')->nullable(false)->change();
            $table->string('identity_number')->nullable(false)->change();
            $table->string('birth_date', 10)->nullable(false)->change();

            $table->unsignedBigInteger('religion_id')->nullable(false)->change();

            $table->longText('address')->nullable(false)->change();

            $table->unsignedBigInteger('province_id')->nullable(false)->change();
            $table->unsignedBigInteger('city_id')->nullable(false)->change();
            $table->unsignedBigInteger('district_id')->nullable(false)->change();
            $table->unsignedBigInteger('sub_district_id')->nullable(false)->change();
            $table->unsignedBigInteger('postal_code_id')->nullable(false)->change();

            $table->string('photo')->nullable(false)->change();
            $table->string('identity_photo')->nullable(false)->change();
        });
    }
};