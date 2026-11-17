<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamen', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('id_perusahaan')->unsigned();
            $table->bigInteger('id_karyawan')->unsigned();
            $table->bigInteger('id_lender')->unsigned();
            $table->bigInteger('id_kyc')->unsigned()->nullable();
            $table->string('id_pinjaman', 255);
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
            $table->double('profesional_fee_1')->default(0);
            $table->double('profesional_fee_2')->default(0);
            $table->double('jumlah_profesional_1')->default(0);
            $table->double('jumlah_profesional_2')->default(0);
            $table->double('ksp_fee')->default(0);
            $table->double('cost_tomi')->nullable();
            $table->double('cost_ksp')->nullable();
            $table->longText('description_cost_tomi')->nullable();
            $table->longText('description_cost_ksp')->nullable();
            $table->enum('status', ['on_proses', 'pending', 'approve', 'done'])->default('on_proses');
            $table->string('referrence_number', 255)->nullable();
            $table->string('document_token', 255)->nullable();
            $table->string('document_name', 255)->nullable();
            $table->enum('ttd', ['y', 'n'])->default('n');
            $table->enum('status_otp', ['pending', 'success'])->default('pending');
            $table->longText('note')->nullable();
            $table->enum('document_privy', ['y', 'n'])->default('n');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['id_pinjaman'], 'pinjamen_id_pinjaman_unique');
            $table->index(['user_id'], 'pinjamen_user_id_foreign');
            $table->index(['id_perusahaan'], 'pinjamen_id_perusahaan_foreign');
            $table->index(['id_karyawan'], 'pinjamen_id_list_data_ksp_foreign');
            $table->index(['id_lender'], 'pinjamen_lenders_FK');
            $table->index(['id_kyc'], 'pinjamen_kyc_users_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamen');
    }
};
