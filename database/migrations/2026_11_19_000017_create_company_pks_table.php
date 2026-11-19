<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_pks', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_perusahaan')->comment('Perusahaan pemilik PKS');
            $table->unsignedBigInteger('feature_id')->comment('Fitur yang dicakup PKS');
            $table->string('nomor_pks', 255);
            $table->date('tanggal_pks');
            $table->date('mulai_berlaku');
            $table->date('berakhir');
            $table->string('file_dokumen', 255)->comment('Nama file di storage/app/pks_perusahaan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['id_perusahaan', 'feature_id'], 'company_pks_perusahaan_feature_index');
            $table->foreign('id_perusahaan', 'company_pks_perusahaan_foreign')->references('id')->on('list_perusahaans')->cascadeOnDelete();
            $table->foreign('feature_id', 'company_pks_feature_foreign')->references('id')->on('features');
            $table->foreign('created_by', 'company_pks_created_by_foreign')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_pks');
    }
};
