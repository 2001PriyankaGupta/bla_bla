<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
         
            $table->enum('user_type', ['passenger', 'driver','support_agent'])->default('passenger');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('locality')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([ 'user_type', 'status', 'locality']);
        });
    }
};