<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_desa_net', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->increments('id');
            $table->string('kecamatan', 255)->nullable();
            $table->string('desa', 255)->nullable();
            $table->string('kodepos', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_desa_net');
    }
};
