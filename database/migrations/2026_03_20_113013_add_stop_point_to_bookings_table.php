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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('stop_point_id')->nullable()->after('ride_id');
            $table->string('drop_point_type')->default('main')->after('stop_point_id'); // 'main' or 'stop'
            
            $table->foreign('stop_point_id')->references('id')->on('stop_points')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['stop_point_id']);
            $table->dropColumn(['stop_point_id', 'drop_point_type']);
        });
    }
};
