<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('RideShare');
            $table->string('logo')->nullable();
            $table->string('contact_email');
            $table->decimal('gst_percentage', 5, 2)->default(0);
            $table->enum('rounding_rules', ['nearest', 'up', 'down'])->default('nearest');
            $table->json('surcharge_configuration')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};