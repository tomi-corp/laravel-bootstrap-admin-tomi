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
        Schema::create('lenders', function (Blueprint $table) {
            $table->id();
            $table->string('id_code_lender')->unique();
            $table->string('pic_lender');
            $table->string('pic_internal');
            $table->string('nama');
            $table->string('email_lender')->unique();
            $table->integer('id_provinsi');
            $table->foreign('id_provinsi')->references('id')->on('wilayah_provinsi_net');
            $table->integer('id_kabupaten');
            $table->foreign('id_kabupaten')->references('id')->on('wilayah_kabupaten_net');
            $table->integer('id_kecamatan');
            $table->foreign('id_kecamatan')->references('id')->on('wilayah_kecamatan_net');
            $table->integer('id_desa');
            $table->foreign('id_desa')->references('id')->on('wilayah_desa_net');
            $table->string('alamat_lengkap');
            $table->string('telp');
            $table->string('nomor_pks');
            $table->string('file_pks');
            $table->date('last_update_file_pks');
            $table->bigInteger('id_bank')->unsigned();
            $table->foreign('id_bank')->references('id')->on('banks');
            $table->string('nama_rekening');
            $table->string('no_rekening');
            $table->string('logo')->nullable();
            $table->string('color')->nullable();
            $table->enum('status', ['active', 'nonactive']);
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('admins');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->foreign('updated_by')->references('id')->on('admins');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lenders');
    }
};
