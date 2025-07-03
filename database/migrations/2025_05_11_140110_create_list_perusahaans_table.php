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
        Schema::create('list_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('id_code_perusahaan')->unique();
            $table->string('pic_perusahaan');
            $table->string('pic_internal');
            $table->string('nama');
            $table->string('email_perusahaan')->unique();
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
            $table->integer('payment_date');
            $table->longText('va_id');
            $table->longText('payment_method_id');
            $table->longText('payment_method_reference_id');
            $table->string('virtual_account_channel_code');
            $table->longText('virtual_account_channel_properties_customer_name');
            $table->longText('virtual_account_channel_properties_virtual_account_number');
            $table->datetimes('virtual_account_channel_properties_virtual_expires_at');
            $table->enum('status', ['active', 'nonactive']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_perusahaans');
    }
};
