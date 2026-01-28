<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['driver', 'passenger']);
            $table->integer('rating'); // 1 to 5
            $table->text('comment')->nullable();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure one review per booking per type
            $table->unique(['booking_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};