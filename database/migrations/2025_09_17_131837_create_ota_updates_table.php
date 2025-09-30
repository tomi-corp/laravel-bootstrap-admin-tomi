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
        Schema::create('ota_updates', function (Blueprint $table) {
            $table->id();
            $table->string('versionMajor');
            $table->string('versionMinor');
            $table->string('otaUpdateID');
            $table->enum('platform', ['android', 'ios']);
            $table->longText('changelog');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ota_updates');
    }
};
