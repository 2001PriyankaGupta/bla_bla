<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('car_make');
            $table->string('car_model');
            $table->integer('car_year');
            $table->string('car_color');
            $table->string('licence_plate')->unique();
            $table->string('car_photo')->nullable();
            $table->string('driver_license_front')->nullable();
            $table->string('driver_license_back')->nullable();
            $table->enum('license_verified', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->string('verified_by')->nullable(); // Admin jo verify karega
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'license_verified']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cars');
    }
};