<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_plus_repayments', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('ksp_admin_id');
            $table->double('total_amount');
            $table->dateTime('payment_date')->nullable();
            $table->date('payment_proof_date')->nullable();
            $table->string('payment_proof_file')->nullable();
            $table->timestamps();
            $table->index('invoice_id', 'loan_plus_repayments_invoice_id_index');
            $table->index('ksp_admin_id', 'loan_plus_repayments_ksp_admin_id_index');
            $table->foreign('invoice_id', 'loan_plus_repayments_invoice_id_fk')->references('id')->on('loan_plus_invoices');
            $table->foreign('ksp_admin_id', 'loan_plus_repayments_ksp_admin_id_fk')->references('id')->on('ksp_admins');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_plus_repayments');
    }
};
