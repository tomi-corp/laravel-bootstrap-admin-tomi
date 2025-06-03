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
        Schema::create('invoicing_pinjamen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->foreign('id_list_perusahaan')->references('id')->on('list_perusahaans');
            $table->date('period');
            $table->bigInteger('total_transaksi_approve');
            $table->double('total_fee_base');
            $table->double('income_ksp');
            $table->double('iuran_anggota');
            $table->enum('status', ['lunas', 'belum_lunas']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoicing_pinjamen');
    }
};
