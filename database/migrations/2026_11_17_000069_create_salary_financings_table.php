<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_financings', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('lender_id')->unsigned()->nullable();
            $table->integer('id_perusahaan')->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('partner_name', 255)->nullable();
            $table->string('file_name', 255);
            $table->string('file_path', 255)->nullable();
            $table->integer('total_borrowers')->unsigned()->default(0);
            $table->decimal('loan_amount', 18, 2)->default(0.00);
            $table->decimal('total_dibayarkan', 18, 2)->default(0.00);
            $table->decimal('total_fee', 18, 2)->default(0.00);
            $table->string('status', 100)->nullable();
            $table->date('agreement_date')->nullable();
            $table->string('agreement_number', 255)->nullable();
            $table->string('agreement_file', 255)->nullable();
            $table->string('pks_number', 255)->nullable();
            $table->string('pks_file', 255)->nullable();
            $table->date('template_uploaded_at')->nullable();
            $table->string('employee_template_file', 255)->nullable();
            $table->string('employee_template_pdf', 255)->nullable();
            $table->decimal('total_transfer_amount', 18, 2)->nullable();
            $table->decimal('tomi_gaji_facility_amount', 18, 2)->nullable();
            $table->string('decision', 20)->nullable();
            $table->string('analyst_decision', 20)->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('receipt_file', 255)->nullable();
            $table->date('transfer_date')->nullable();
            $table->date('transfer_proof_date')->nullable();
            $table->string('transfer_proof_file', 255)->nullable();
            $table->date('bilyet_giro_received_date')->nullable();
            $table->date('bilyet_giro_date')->nullable();
            $table->string('bilyet_giro_file', 255)->nullable();
            $table->date('payment_date')->nullable();
            $table->date('payment_proof_date')->nullable();
            $table->string('payment_proof_file', 255)->nullable();
            $table->string('bilyet_giro_validation_status', 50)->nullable();
            $table->date('bilyet_giro_validation_date')->nullable();
            $table->string('bilyet_giro_validation_document', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_financings');
    }
};
