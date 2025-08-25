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
        Schema::create('list_karyawans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->foreign('id_list_perusahaan')->references('id')->on('list_perusahaans');
            $table->string('nik')->unique();
            $table->string('nip')->unique();
            $table->string('whatsapp')->unique();
            $table->string('nama_karyawan');
            $table->string('alamat');
            $table->string('divisi');
            $table->string('jabatan');
            $table->string('max_plafon');
            $table->string('departement');
            $table->date('tanggal_bergabung');
            $table->enum('status', ['tetap', 'percobaan', 'phk', 'resign']);
            $table->enum('status_sp', ['0', '1', '2', '3']);
            $table->bigInteger('id_nama_bank')->unsigned();
            $table->foreign('id_nama_bank')->references('id')->on('banks');
            $table->longText('nama_rekening');
            $table->longText('nomor_bank');
            $table->enum('approve_satu', ['pending', 'approve', 'reject']);
            $table->enum('approve_dua', ['pending', 'approve', 'reject']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_karyawans');
    }
};
