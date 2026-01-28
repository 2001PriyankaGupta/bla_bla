<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_rides_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_point');
            $table->string('drop_point');
            $table->datetime('date_time');
            $table->integer('total_seats');
            $table->decimal('price_per_seat', 8, 2);
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->boolean('luggage_allowed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rides');
    }
};