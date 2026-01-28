<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_user_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Unique constraint to prevent duplicates
            $table->unique(['admin_user_id', 'permission_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_user_permission');
    }
};