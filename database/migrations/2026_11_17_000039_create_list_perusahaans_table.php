<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_perusahaans', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->string('unique_code', 100)->nullable();
            $table->bigInteger('id_lender')->unsigned()->nullable();
            $table->string('id_code_perusahaan', 255);
            $table->string('pic_perusahaan', 255);
            $table->string('pic_internal', 255);
            $table->string('nama', 255);
            $table->string('logo', 255)->nullable();
            $table->string('email_perusahaan', 255)->nullable();
            $table->integer('id_provinsi');
            $table->integer('id_kabupaten');
            $table->integer('id_kecamatan');
            $table->integer('id_desa');
            $table->string('alamat_lengkap', 255);
            $table->string('bank', 255)->nullable();
            $table->string('no_rekening', 255)->nullable();
            $table->string('atas_nama_rekening', 255)->nullable();
            $table->string('nama_direktur_utama', 255)->nullable();
            $table->string('telp', 255);
            $table->string('nomor_pks', 255);
            $table->integer('payment_date');
            $table->integer('h_min_jatuh_tempo');
            $table->enum('status', ['active', 'nonactive']);
            $table->enum('active_profesional_fee', ['active', 'nonactive']);
            $table->enum('active_set_pinjaman', ['active', 'nonactive']);
            $table->integer('active_tanggal');
            $table->string('max_plafon', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['id_code_perusahaan'], 'list_perusahaans_id_code_perusahaan_unique');
            $table->unique(['unique_code'], 'list_perusahaans_unique');
            $table->unique(['email_perusahaan'], 'list_perusahaans_unique_1');
            $table->index(['id_provinsi'], 'list_perusahaans_id_provinsi_foreign');
            $table->index(['id_kabupaten'], 'list_perusahaans_id_kabupaten_foreign');
            $table->index(['id_kecamatan'], 'list_perusahaans_id_kecamatan_foreign');
            $table->index(['id_desa'], 'list_perusahaans_id_desa_foreign');
            $table->index(['id_lender'], 'list_perusahaans_lenders_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_perusahaans');
    }
};
