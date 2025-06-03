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
        Schema::create('list_data_ksps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->foreign('id_list_perusahaan')->references('id')->on('list_perusahaans');
            $table->string('nik')->unique();
            $table->string('nip')->unique();
            $table->string('whatsapp')->unique();
            $table->string('nama_karyawan');
            $table->string('alamat');
            $table->string('jabatan');
            $table->string('penghasilan');
            $table->string('departement');
            $table->date('tanggal_bergabung');
            $table->string('status');
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
        Schema::dropIfExists('list_data_ksps');
    }
};
