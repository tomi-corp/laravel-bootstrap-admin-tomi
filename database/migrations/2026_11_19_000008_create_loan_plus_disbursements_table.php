<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_plus_disbursements', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('ksp_admin_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('bank_id');
            $table->double('disbursed_amount');
            $table->string('account_name');
            $table->string('account_number');
            $table->date('transfer_date')->nullable();
            $table->string('transfer_proof')->nullable();
            $table->timestamps();
            $table->index('loan_id', 'loan_plus_disbursements_loan_id_index');
            $table->index('ksp_admin_id', 'loan_plus_disbursements_ksp_admin_id_index');
            $table->index('bank_id', 'loan_plus_disbursements_bank_id_index');
            $table->index('admin_id', 'loan_plus_disbursements_admin_id_index');
            $table->foreign('loan_id', 'loan_plus_disbursements_loan_id_fk')->references('id')->on('loan_plus');
            $table->foreign('ksp_admin_id', 'loan_plus_disbursements_ksp_admin_id_fk')->references('id')->on('ksp_admins');
            $table->foreign('admin_id', 'loan_plus_disbursements_admin_id_fk')->references('id')->on('admins');
            $table->foreign('bank_id', 'loan_plus_disbursements_bank_id_fk')->references('id')->on('banks');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_plus_disbursements');
    }
};
