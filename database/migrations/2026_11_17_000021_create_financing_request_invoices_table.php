<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financing_request_invoices', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('lender_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('vendor_id')->unsigned();
            $table->bigInteger('vendor_payment_term_id')->unsigned()->nullable();
            $table->date('quotation_date')->nullable();
            $table->string('quotation_number', 255)->nullable();
            $table->string('quotation_file', 255)->nullable();
            $table->string('kspddt_mitra_agreement_file', 255)->nullable()->comment('File perjanjian antara KSPDDT dan mitra');
            $table->date('mitra_pelanggan_agreement_date')->nullable()->comment('Tanggal perjanjian antara mitra dan pelanggan');
            $table->string('mitra_pelanggan_agreement_number', 255)->nullable()->comment('Nomor perjanjian antara mitra dan pelanggan');
            $table->string('mitra_pelanggan_agreement_file', 255)->nullable()->comment('File perjanjian antara mitra dan pelanggan');
            $table->date('purchase_order_date')->nullable();
            $table->string('purchase_order_number', 255)->nullable();
            $table->string('purchase_order_file', 255)->nullable();
            $table->decimal('purchase_order_amount', 15, 2)->default(0.00);
            $table->decimal('approved_financing_amount', 15, 2)->nullable();
            $table->enum('analyst_decision', ['pending', 'approved', 'rejected', 'revision'])->default('pending');
            $table->text('analyst_note')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->bigInteger('approved_by')->unsigned()->nullable();
            $table->string('finance_spp_file', 255)->nullable();
            $table->date('bilyet_giro_date_1')->nullable();
            $table->string('bilyet_giro_file_1', 255)->nullable();
            $table->date('bilyet_giro_deposit_date_1')->nullable()->comment('Tanggal setor bilyet giro termin 1');
            $table->string('bilyet_giro_deposit_file_1', 255)->nullable()->comment('File bukti setor bilyet giro termin 1');
            $table->date('bilyet_giro_received_date_1')->nullable();
            $table->string('bilyet_giro_validation_status_1', 255)->nullable()->comment('Status validasi bilyet giro termin 1');
            $table->date('bilyet_giro_validation_date_1')->nullable()->comment('Tanggal validasi bilyet giro termin 1');
            $table->date('bilyet_giro_date_2')->nullable();
            $table->string('bilyet_giro_file_2', 255)->nullable();
            $table->date('bilyet_giro_deposit_date_2')->nullable()->comment('Tanggal setor bilyet giro termin 2');
            $table->string('bilyet_giro_deposit_file_2', 255)->nullable()->comment('File bukti setor bilyet giro termin 2');
            $table->date('bilyet_giro_received_date_2')->nullable();
            $table->string('bilyet_giro_validation_status_2', 255)->nullable()->comment('Status validasi bilyet giro termin 2');
            $table->date('bilyet_giro_validation_date_2')->nullable()->comment('Tanggal validasi bilyet giro termin 2');
            $table->date('bilyet_giro_date_3')->nullable();
            $table->string('bilyet_giro_file_3', 255)->nullable();
            $table->date('bilyet_giro_deposit_date_3')->nullable()->comment('Tanggal setor bilyet giro termin 3');
            $table->string('bilyet_giro_deposit_file_3', 255)->nullable()->comment('File bukti setor bilyet giro termin 3');
            $table->date('bilyet_giro_received_date_3')->nullable();
            $table->string('bilyet_giro_validation_status_3', 255)->nullable()->comment('Status validasi bilyet giro termin 3');
            $table->date('bilyet_giro_validation_date_3')->nullable()->comment('Tanggal validasi bilyet giro termin 3');
            $table->date('handover_report_date_2')->nullable();
            $table->string('handover_report_file_2', 255)->nullable();
            $table->date('handover_report_date_3')->nullable();
            $table->string('handover_report_file_3', 255)->nullable();
            $table->date('finance_vendor_payment_proof_date_1')->nullable();
            $table->decimal('finance_vendor_payment_proof_amount_1', 18, 2)->nullable()->comment('Nominal pembayaran Finance ke Vendor Termin 1');
            $table->string('finance_vendor_payment_proof_file_1', 255)->nullable();
            $table->date('finance_vendor_payment_proof_date_2')->nullable();
            $table->decimal('finance_vendor_payment_proof_amount_2', 18, 2)->nullable()->comment('Nominal pembayaran Finance ke Vendor Termin 2');
            $table->string('finance_vendor_payment_proof_file_2', 255)->nullable();
            $table->date('finance_vendor_payment_proof_date_3')->nullable();
            $table->decimal('finance_vendor_payment_proof_amount_3', 18, 2)->nullable()->comment('Nominal pembayaran Finance ke Vendor Termin 3');
            $table->string('finance_vendor_payment_proof_file_3', 255)->nullable();
            $table->decimal('finance_invoice_term_amount_1', 15, 2)->nullable();
            $table->date('finance_invoice_term_date_1')->nullable();
            $table->string('finance_invoice_term_file_1', 255)->nullable();
            $table->decimal('finance_invoice_term_amount_2', 15, 2)->nullable();
            $table->date('finance_invoice_term_date_2')->nullable();
            $table->string('finance_invoice_term_file_2', 255)->nullable();
            $table->decimal('finance_invoice_term_amount_3', 15, 2)->nullable();
            $table->date('finance_invoice_term_date_3')->nullable();
            $table->string('finance_invoice_term_file_3', 255)->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
            $table->date('referral_transfer_date_1')->nullable();
            $table->string('referral_transfer_proof_1', 255)->nullable();
            $table->date('referral_transfer_date_2')->nullable();
            $table->string('referral_transfer_proof_2', 255)->nullable();
            $table->date('referral_transfer_date_3')->nullable();
            $table->string('referral_transfer_proof_3', 255)->nullable();
            $table->index(['client_id'], 'idx_financing_request_client_id');
            $table->index(['vendor_id'], 'idx_financing_request_vendor_id');
            $table->index(['vendor_payment_term_id'], 'idx_financing_request_vendor_payment_term_id');
            $table->index(['approved_by'], 'idx_financing_request_approved_by');
            $table->index(['analyst_decision'], 'idx_financing_request_analyst_decision');
            $table->index(['status'], 'idx_financing_request_status');
            $table->index(['deleted_at'], 'idx_financing_request_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financing_request_invoices');
    }
};
