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
        Schema::create('daily_checkin_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('daily_checkin_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->double('ticket');
            $table->longText('description');
            $table->date('expired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_checkin_histories');
    }
};
