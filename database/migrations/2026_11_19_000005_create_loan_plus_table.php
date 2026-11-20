<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_plus', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('lender_id');
            $table->unsignedBigInteger('kyc_id')->nullable();
            $table->string('loan_number');
            $table->double('principal_amount');
            $table->integer('tenor');
            $table->double('rate');
            $table->double('insurance_rate');
            $table->double('provision_rate');
            $table->double('admin_fee_rate');
            $table->double('member_contribution');
            $table->double('total_admin_fee');
            $table->double('ksp_admin_fee');
            $table->double('ksp_admin_tax_base');
            $table->double('ksp_admin_vat');
            $table->double('pandu_admin_fee');
            $table->double('pandu_admin_tax_base');
            $table->double('pandu_admin_vat');
            $table->double('interest_income');
            $table->double('interest_tax_base');
            $table->double('interest_vat');
            $table->double('service_fee');
            $table->double('monthly_installment');
            $table->double('net_disbursement_amount');
            $table->double('insurance_amount');
            $table->double('provision_amount');
            $table->double('professional_fee_1')->default(0);
            $table->double('professional_fee_2')->default(0);
            $table->double('professional_fee_1_amount')->default(0);
            $table->double('professional_fee_2_amount')->default(0);
            $table->double('ksp_fee')->default(0);
            $table->double('tomi_cost')->nullable();
            $table->double('ksp_cost')->nullable();
            $table->longText('tomi_cost_description')->nullable();
            $table->longText('ksp_cost_description')->nullable();
            $table->enum('status', ['in_process', 'pending', 'approved', 'completed'])->default('in_process');
            $table->string('reference_number')->nullable();
            $table->string('document_token')->nullable();
            $table->string('document_name')->nullable();
            $table->enum('signature_status', ['yes', 'no'])->default('no');
            $table->enum('otp_status', ['pending', 'success'])->default('pending');
            $table->longText('note')->nullable();
            $table->enum('privy_document_status', ['yes', 'no'])->default('no');
            $table->softDeletes();
            $table->timestamps();
            $table->unique('loan_number', 'loan_plus_loan_number_unique');
            $table->index('user_id', 'loan_plus_user_id_index');
            $table->index('company_id', 'loan_plus_company_id_index');
            $table->index('employee_id', 'loan_plus_employee_id_index');
            $table->index('lender_id', 'loan_plus_lender_id_index');
            $table->index('kyc_id', 'loan_plus_kyc_id_index');
            $table->foreign('user_id', 'loan_plus_user_id_fk')->references('id')->on('users');
            $table->foreign('company_id', 'loan_plus_company_id_fk')->references('id')->on('list_perusahaans');
            $table->foreign('employee_id', 'loan_plus_employee_id_fk')->references('id')->on('list_karyawans');
            $table->foreign('lender_id', 'loan_plus_lender_id_fk')->references('id')->on('lenders');
            $table->foreign('kyc_id', 'loan_plus_kyc_id_fk')->references('id')->on('kyc_users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_plus');
    }
};
