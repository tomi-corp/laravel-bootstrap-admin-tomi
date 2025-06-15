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
        Schema::create('pinjamen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('id_perusahaan')->unsigned();
            $table->foreign('id_perusahaan')->references('id')->on('list_perusahaans');
            $table->bigInteger('id_list_data_ksp')->unsigned();
            $table->foreign('id_list_data_ksp')->references('id')->on('list_data_ksps');
            $table->string('id_pinjaman')->unique();
            $table->double('nominal');
            $table->integer('tenor');
            $table->double('rate');
            $table->double('biaya_admin');
            $table->double('iuran_anggota');
            $table->double('total_biaya_admin');

            $table->double('biaya_admin_ksp');
            $table->double('dpp_adm_ksp');
            $table->double('ppn_adm_ksp');
            $table->double('biaya_admin_pandu');
            $table->double('dpp_adm_pandu');
            $table->double('ppn_adm_pandu');
            $table->double('pendapatan_bunga');
            $table->double('dpp_bunga');
            $table->double('ppn_bunga');
            $table->double('biaya_layanan');

            $table->double('total_angsuran_perbulan');
            $table->double('jumlah_yang_diterima');
            $table->enum('status', ['on_proses', 'exported', 'approve', 'reject', 'done'])->default('on_proses');
            $table->enum('status_otp', ['pending', 'success'])->default('pending');
            $table->longText('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjamen');
    }
};
