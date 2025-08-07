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
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pinjaman_id')->unsigned();
            $table->foreign('pinjaman_id')->references('id')->on('pinjamen');
            $table->bigInteger('ksp_admin_id')->unsigned();
            $table->foreign('ksp_admin_id')->references('id')->on('ksp_admins');
            $table->bigInteger('bank_id')->unsigned();
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->double('dana_yang_dicairkan');
            $table->string('atas_nama');
            $table->string('nomor_rekening');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursements');
    }
};
