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
        Schema::create('daftar_e_kasbons', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('jumlah_karyawan');
            $table->string('pic');
            $table->string('whatsapp');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_e_kasbons');
    }
};
