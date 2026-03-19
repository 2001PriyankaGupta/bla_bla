<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('rejection_reason');
            
            // Drop the problematic unique constraint
            // The constraint name is usually table_column1_column2_column3_unique
            $table->dropUnique(['ride_id', 'user_id', 'status']);
            
            // Add a more sensible unique constraint: only one active booking per ride/user
            // We can't easily do conditional unique in MySQL via Blueprint, 
            // but at least removing the old one allows multiple cancellations.
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
            $table->unique(['ride_id', 'user_id', 'status']);
        });
    }
};
