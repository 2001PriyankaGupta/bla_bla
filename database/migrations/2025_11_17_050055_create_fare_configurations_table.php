<?php
// database/migrations/2024_01_01_000001_create_fare_configurations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fare_configurations', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_fare', 10, 2)->default(0);
            $table->decimal('per_km_charge', 10, 2)->default(0);
            $table->decimal('waiting_fee', 10, 2)->default(0);
            $table->decimal('home_pickup_fee', 10, 2)->default(0);
            $table->decimal('night_holiday_surcharge', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fare_configurations');
    }
};