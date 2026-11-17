<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disbursements', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('pinjaman_id')->unsigned();
            $table->bigInteger('ksp_admin_id')->unsigned();
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->bigInteger('bank_id')->unsigned();
            $table->double('dana_yang_dicairkan');
            $table->string('atas_nama', 255);
            $table->string('nomor_rekening', 255);
            $table->date('transfer_date')->nullable();
            $table->string('transfer_proof', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['pinjaman_id'], 'disbursements_pinjaman_id_foreign');
            $table->index(['ksp_admin_id'], 'disbursements_ksp_admin_id_foreign');
            $table->index(['bank_id'], 'disbursements_bank_id_foreign');
            $table->index(['admin_id'], 'idx_disbursements_admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disbursements');
    }
};
