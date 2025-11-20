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
            $table->bigInteger('id_karyawan')->unsigned();
            $table->foreign('id_karyawan')->references('id')->on('list_karyawans');
            $table->bigInteger('id_lender')->unsigned();
            $table->foreign('id_lender')->references('id')->on('lenders');
            $table->bigInteger('id_kyc')->unsigned();
            $table->foreign('id_kyc')->references('id')->on('kyc_users');
            $table->string('id_pinjaman')->unique();
            $table->double('nominal');
            $table->integer('tenor');
            $table->double('rate');
            $table->double('asuransi');
            $table->double('provisi');
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
            $table->double('jumlah_asuransi');
            $table->double('jumlah_provisi');

            $table->double('cost_tomi');
            $table->double('cost_ksp');
            $table->longText('description_cost_tomi');
            $table->longText('description_cost_ksp');

            $table->string('referrence_number')->nullable();
            $table->string('document_token')->nullable();
            $table->string('document_name')->nullable();
            $table->enum('ttd', ['y', 'n'])->default('n');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('status', ['on_proses', 'pending', 'approve', 'done'])->default('on_proses');
            $table->enum('status_otp', ['pending', 'success'])->default('pending');
            $table->enum('document_privy', ['y', 'n'])->default('n');
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
