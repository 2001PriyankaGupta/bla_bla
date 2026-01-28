<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('rating', 3, 2)->default(0.00)->after('profile_picture');
            $table->integer('total_rides')->default(0)->after('rating');
            $table->integer('cancelled_rides')->default(0)->after('total_rides');
            $table->text('bio')->nullable()->after('cancelled_rides');
            $table->json('languages')->nullable()->after('bio');
            $table->boolean('is_verified')->default(false)->after('languages');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rating', 'total_rides', 'cancelled_rides', 'bio', 'languages', 'is_verified']);
        });
    }
};