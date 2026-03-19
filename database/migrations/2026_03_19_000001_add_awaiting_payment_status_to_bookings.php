<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add awaiting_payment to status enum in bookings table
        // For MySQL, we use RAW SQL for enum updates
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'rejected', 'cancelled', 'completed', 'awaiting_payment') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'rejected', 'cancelled', 'completed') DEFAULT 'pending'");
    }
};
