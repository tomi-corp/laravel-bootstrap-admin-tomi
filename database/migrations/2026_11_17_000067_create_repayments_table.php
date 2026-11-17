<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_invoicing')->unsigned();
            $table->bigInteger('ksp_admin_id')->unsigned();
            $table->double('amount_total');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->date('payment_proof_date')->nullable();
            $table->string('payment_proof_file', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_invoicing'], 'repayments_id_invoicing_foreign');
            $table->index(['ksp_admin_id'], 'repayments_ksp_admin_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
