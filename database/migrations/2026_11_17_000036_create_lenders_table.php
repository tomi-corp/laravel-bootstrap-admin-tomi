<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lenders', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->string('id_code_lender', 255);
            $table->string('pic_lender', 255);
            $table->string('pic_internal', 255);
            $table->string('nama', 255);
            $table->string('email_lender', 255);
            $table->integer('id_provinsi');
            $table->integer('id_kabupaten');
            $table->integer('id_kecamatan');
            $table->integer('id_desa');
            $table->string('alamat_lengkap', 255);
            $table->string('telp', 255);
            $table->string('nomor_pks', 255);
            $table->string('file_pks', 255);
            $table->date('last_update_file_pks');
            $table->bigInteger('id_bank')->unsigned();
            $table->string('nama_rekening', 255);
            $table->string('no_rekening', 255);
            $table->string('logo', 255)->nullable();
            $table->string('color', 255)->nullable();
            $table->enum('status', ['active', 'nonactive']);
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['id_code_lender'], 'lenders_id_code_lender_unique');
            $table->unique(['email_lender'], 'lenders_email_lender_unique');
            $table->index(['id_provinsi'], 'lenders_id_provinsi_foreign');
            $table->index(['id_kabupaten'], 'lenders_id_kabupaten_foreign');
            $table->index(['id_kecamatan'], 'lenders_id_kecamatan_foreign');
            $table->index(['id_desa'], 'lenders_id_desa_foreign');
            $table->index(['id_bank'], 'lenders_id_bank_foreign');
            $table->index(['created_by'], 'lenders_created_by_foreign');
            $table->index(['updated_by'], 'lenders_updated_by_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lenders');
    }
};
