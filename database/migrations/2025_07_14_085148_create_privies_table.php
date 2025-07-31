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
        Schema::create('privies', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('channel_id');
            $table->string('token');
            $table->datetimes('expired');
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privies');
    }
};
