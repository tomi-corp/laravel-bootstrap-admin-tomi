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
            $table->unsignedBigInteger('company_id')->comment('Perusahaan pemilik PKS (list_perusahaans.id)');
            $table->unsignedBigInteger('feature_id')->comment('Fitur yang dicakup PKS');
            $table->string('agreement_number', 255)->comment('Nomor PKS');
            $table->date('agreement_date')->comment('Tanggal PKS');
            $table->date('start_date')->comment('Tanggal mulai berlaku');
            $table->date('end_date')->comment('Tanggal berakhir');
            $table->string('document_file', 255)->comment('Nama file di storage/app/pks_perusahaan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'feature_id'], 'company_pks_company_feature_index');
            $table->foreign('company_id', 'company_pks_company_foreign')->references('id')->on('list_perusahaans')->cascadeOnDelete();
            $table->foreign('feature_id', 'company_pks_feature_foreign')->references('id')->on('features');
            $table->foreign('created_by', 'company_pks_created_by_foreign')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_pks');
    }
};
