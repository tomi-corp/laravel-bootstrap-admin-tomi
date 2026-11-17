<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_dati', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->bigInteger('id')->unsigned()->nullable();
            $table->string('kabupaten', 225)->nullable();
            $table->string('sandi', 225)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_dati');
    }
};
