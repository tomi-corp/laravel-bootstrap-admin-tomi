<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amount_pinjamen', function (Blueprint $table) {
            $table->foreign(['id_perusahaan'], 'amount_pinjamen_list_perusahaans_FK')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['bank_id'], 'fk_clients_bank')->references(['id'])->on('banks')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['city_id'], 'fk_clients_city')->references(['id'])->on('wilayah_kabupaten_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['district_id'], 'fk_clients_district')->references(['id'])->on('wilayah_kecamatan_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['province_id'], 'fk_clients_province')->references(['id'])->on('wilayah_provinsi_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['village_id'], 'fk_clients_village')->references(['id'])->on('wilayah_desa_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('company_feature_referrals', function (Blueprint $table) {
            $table->foreign(['company_id'], 'fk_company_feature_referrals_company')->references(['id'])->on('list_perusahaans')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('company_feature_referrals', function (Blueprint $table) {
            $table->foreign(['feature_id'], 'fk_company_feature_referrals_feature')->references(['id'])->on('features')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('company_feature_referrals', function (Blueprint $table) {
            $table->foreign(['referral_id'], 'fk_company_feature_referrals_referral')->references(['id'])->on('referrals')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('company_referrals', function (Blueprint $table) {
            $table->foreign(['company_id'], 'fk_company_referrals_company')->references(['id'])->on('list_perusahaans')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('company_referrals', function (Blueprint $table) {
            $table->foreign(['referral_id'], 'fk_company_referrals_referral')->references(['id'])->on('referrals')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->foreign(['bank_id'], 'disbursements_bank_id_foreign')->references(['id'])->on('banks');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->foreign(['ksp_admin_id'], 'disbursements_ksp_admin_id_foreign')->references(['id'])->on('ksp_admins');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->foreign(['pinjaman_id'], 'disbursements_pinjaman_id_foreign')->references(['id'])->on('pinjamen');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->foreign(['admin_id'], 'fk_disbursements_admin_id')->references(['id'])->on('admins')->onUpdate('cascade');
        });
        Schema::table('financing_request_configurations', function (Blueprint $table) {
            $table->foreign(['id_perusahaan'], 'financing_request_configurations_id_perusahaan_fk')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->foreign(['approved_by'], 'fk_financing_request_approved_by')->references(['id'])->on('admins')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->foreign(['client_id'], 'fk_financing_request_client')->references(['id'])->on('clients')->onUpdate('cascade');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->foreign(['vendor_id'], 'fk_financing_request_vendor')->references(['id'])->on('vendors')->onUpdate('cascade');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->foreign(['vendor_payment_term_id'], 'fk_financing_request_vendor_payment_term')->references(['id'])->on('vendor_payment_terms')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('history_invoicing_lenders', function (Blueprint $table) {
            $table->foreign(['update_by'], 'history_invoicing_lenders_admins_FK')->references(['id'])->on('admins');
        });
        Schema::table('history_invoicing_lenders', function (Blueprint $table) {
            $table->foreign(['id_lender'], 'history_invoicing_lenders_lenders_FK')->references(['id'])->on('lenders');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->foreign(['first_approver_id'], 'history_invoicing_lender_updates_first_approver_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->foreign(['invoicing_lender_id'], 'history_invoicing_lender_updates_invoicing_lender_id_foreign')->references(['id'])->on('history_invoicing_lenders');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->foreign(['requester_id'], 'history_invoicing_lender_updates_requester_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->foreign(['second_approver_id'], 'history_invoicing_lender_updates_second_approver_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('history_invoicing_payments', function (Blueprint $table) {
            $table->foreign(['metadata_sku'], 'history_invoicing_payments_metadata_sku_foreign')->references(['id_code_perusahaan'])->on('list_perusahaans');
        });
        Schema::table('invoicing_pinjamen', function (Blueprint $table) {
            $table->foreign(['id_list_perusahaan'], 'invoicing_pinjamen_id_list_perusahaan_foreign')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('invoicing_pinjamen', function (Blueprint $table) {
            $table->foreign(['id_list_perusahaan'], 'invoicing_pinjamen_list_perusahaans_FK')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('jatuh_tempos', function (Blueprint $table) {
            $table->foreign(['id_pinjaman'], 'jatuh_tempos_id_pinjaman_foreign')->references(['id'])->on('pinjamen');
        });
        Schema::table('jatuh_tempos', function (Blueprint $table) {
            $table->foreign(['id_pinjaman'], 'jatuh_tempos_pinjamen_FK')->references(['id'])->on('pinjamen');
        });
        Schema::table('ksp_admins', function (Blueprint $table) {
            $table->foreign(['id_lender'], 'ksp_admins_lenders_FK')->references(['id'])->on('lenders');
        });
        Schema::table('kyc_users', function (Blueprint $table) {
            $table->foreign(['user_id'], 'kyc_users_user_id_foreign')->references(['id'])->on('users');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['created_by'], 'lenders_created_by_foreign')->references(['id'])->on('admins');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['id_bank'], 'lenders_id_bank_foreign')->references(['id'])->on('banks');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['id_desa'], 'lenders_id_desa_foreign')->references(['id'])->on('wilayah_desa_net');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['id_kabupaten'], 'lenders_id_kabupaten_foreign')->references(['id'])->on('wilayah_kabupaten_net');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['id_kecamatan'], 'lenders_id_kecamatan_foreign')->references(['id'])->on('wilayah_kecamatan_net');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['id_provinsi'], 'lenders_id_provinsi_foreign')->references(['id'])->on('wilayah_provinsi_net');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->foreign(['updated_by'], 'lenders_updated_by_foreign')->references(['id'])->on('admins');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->foreign(['first_approver_id'], 'lender_updates_first_approver_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->foreign(['lender_id'], 'lender_updates_lender_id_foreign')->references(['id'])->on('lenders');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->foreign(['requester_id'], 'lender_updates_requester_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->foreign(['second_approver_id'], 'lender_updates_second_approver_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('list_karyawans', function (Blueprint $table) {
            $table->foreign(['id_nama_bank'], 'list_karyawans_banks_FK')->references(['id'])->on('banks');
        });
        Schema::table('list_karyawans', function (Blueprint $table) {
            $table->foreign(['id_list_perusahaan'], 'list_karyawans_id_list_perusahaan_foreign')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreign(['id_desa'], 'list_perusahaans_id_desa_foreign')->references(['id'])->on('wilayah_desa_net');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreign(['id_kabupaten'], 'list_perusahaans_id_kabupaten_foreign')->references(['id'])->on('wilayah_kabupaten_net');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreign(['id_kecamatan'], 'list_perusahaans_id_kecamatan_foreign')->references(['id'])->on('wilayah_kecamatan_net');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreign(['id_provinsi'], 'list_perusahaans_id_provinsi_foreign')->references(['id'])->on('wilayah_provinsi_net');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreign(['id_lender'], 'list_perusahaans_lenders_FK')->references(['id'])->on('lenders');
        });
        Schema::table('partner_companies', function (Blueprint $table) {
            $table->foreign(['company_id'], 'fk_partner_companies_company')->references(['id'])->on('list_perusahaans')->onDelete('cascade');
        });
        Schema::table('perubahaan_emails', function (Blueprint $table) {
            $table->foreign(['user_id'], 'perubahaan_emails_user_id_foreign')->references(['id'])->on('users');
        });
        Schema::table('pinjaman_pending_notifications', function (Blueprint $table) {
            $table->foreign(['pinjaman_id'], 'pinjaman_pending_notifications_pinjaman_id_foreign')->references(['id'])->on('pinjamen');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->foreign(['id_perusahaan'], 'pinjamen_id_perusahaan_foreign')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->foreign(['id_kyc'], 'pinjamen_kyc_users_FK')->references(['id'])->on('kyc_users');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->foreign(['id_lender'], 'pinjamen_lenders_FK')->references(['id'])->on('lenders');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->foreign(['id_karyawan'], 'pinjamen_list_karyawans_FK')->references(['id'])->on('list_karyawans');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->foreign(['user_id'], 'pinjamen_user_id_foreign')->references(['id'])->on('users');
        });
        Schema::table('point_histories', function (Blueprint $table) {
            $table->foreign(['user_id'], 'point_histories_users_FK')->references(['id'])->on('users');
        });
        Schema::table('privy_pses', function (Blueprint $table) {
            $table->foreign(['user_id'], 'privy_pses_user_id_foreign')->references(['id'])->on('users');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign(['bank_id'], 'fk_referrals_bank')->references(['id'])->on('banks')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign(['city_id'], 'fk_referrals_city')->references(['id'])->on('wilayah_kabupaten_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign(['district_id'], 'fk_referrals_district')->references(['id'])->on('wilayah_kecamatan_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign(['lender_id'], 'fk_referrals_lender')->references(['id'])->on('lenders')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign(['province_id'], 'fk_referrals_province')->references(['id'])->on('wilayah_provinsi_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreign(['village_id'], 'fk_referrals_village')->references(['id'])->on('wilayah_desa_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('rekening_pandus', function (Blueprint $table) {
            $table->foreign(['bank_id'], 'rekening_pandus_bank_id_foreign')->references(['id'])->on('banks');
        });
        Schema::table('rekening_pandus', function (Blueprint $table) {
            $table->foreign(['updated_by'], 'rekening_pandus_updated_by_foreign')->references(['id'])->on('admins');
        });
        Schema::table('rekening_tomis', function (Blueprint $table) {
            $table->foreign(['updated_by'], 'rekening_tomis_admins_FK')->references(['id'])->on('admins');
        });
        Schema::table('rekening_tomis', function (Blueprint $table) {
            $table->foreign(['bank_id'], 'rekening_tomis_bank_id_foreign')->references(['id'])->on('banks');
        });
        Schema::table('repayments', function (Blueprint $table) {
            $table->foreign(['id_invoicing'], 'repayments_id_invoicing_foreign')->references(['id'])->on('invoicing_pinjamen');
        });
        Schema::table('repayments', function (Blueprint $table) {
            $table->foreign(['ksp_admin_id'], 'repayments_ksp_admin_id_foreign')->references(['id'])->on('ksp_admins');
        });
        Schema::table('role_ksps', function (Blueprint $table) {
            $table->foreign(['admin_id'], 'role_ksps_admin_id_foreign')->references(['id'])->on('ksp_admins');
        });
        Schema::table('salary_financing_configurations', function (Blueprint $table) {
            $table->foreign(['id_perusahaan'], 'salary_financing_configurations_id_perusahaan_fk')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('tenor_pinjamen', function (Blueprint $table) {
            $table->foreign(['id_perusahaan'], 'tenor_pinjamen_list_perusahaans_FK')->references(['id'])->on('list_perusahaans');
        });
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->foreign(['admin_id'], 'user_activity_logs_admin_id_foreign')->references(['id'])->on('admins');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign(['bank_id'], 'fk_vendors_bank')->references(['id'])->on('banks')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign(['city_id'], 'fk_vendors_city')->references(['id'])->on('wilayah_kabupaten_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign(['district_id'], 'fk_vendors_district')->references(['id'])->on('wilayah_kecamatan_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign(['province_id'], 'fk_vendors_province')->references(['id'])->on('wilayah_provinsi_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign(['village_id'], 'fk_vendors_village')->references(['id'])->on('wilayah_desa_net')->onDelete('set null')->onUpdate('cascade');
        });
        Schema::table('vendor_payment_terms', function (Blueprint $table) {
            $table->foreign(['vendor_id'], 'fk_vendor_payment_terms_vendor')->references(['id'])->on('vendors')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::table('verif_emails', function (Blueprint $table) {
            $table->foreign(['id_user'], 'verif_emails_users_FK')->references(['id'])->on('users');
        });
        Schema::table('verif_otp_pengajuans', function (Blueprint $table) {
            $table->foreign(['pinjaman_id'], 'verif_otp_pengajuans_pinjaman_id_foreign')->references(['id'])->on('pinjamen');
        });
        Schema::table('verif_otp_pengajuans', function (Blueprint $table) {
            $table->foreign(['user_id'], 'verif_otp_pengajuans_user_id_foreign')->references(['id'])->on('users');
        });
        Schema::table('verif_whatsapps', function (Blueprint $table) {
            $table->foreign(['id_user'], 'verif_whatsapps_users_FK')->references(['id'])->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('verif_whatsapps', function (Blueprint $table) {
            $table->dropForeign('verif_whatsapps_users_FK');
        });
        Schema::table('verif_otp_pengajuans', function (Blueprint $table) {
            $table->dropForeign('verif_otp_pengajuans_user_id_foreign');
        });
        Schema::table('verif_otp_pengajuans', function (Blueprint $table) {
            $table->dropForeign('verif_otp_pengajuans_pinjaman_id_foreign');
        });
        Schema::table('verif_emails', function (Blueprint $table) {
            $table->dropForeign('verif_emails_users_FK');
        });
        Schema::table('vendor_payment_terms', function (Blueprint $table) {
            $table->dropForeign('fk_vendor_payment_terms_vendor');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('fk_vendors_village');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('fk_vendors_province');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('fk_vendors_district');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('fk_vendors_city');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('fk_vendors_bank');
        });
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropForeign('user_activity_logs_admin_id_foreign');
        });
        Schema::table('tenor_pinjamen', function (Blueprint $table) {
            $table->dropForeign('tenor_pinjamen_list_perusahaans_FK');
        });
        Schema::table('salary_financing_configurations', function (Blueprint $table) {
            $table->dropForeign('salary_financing_configurations_id_perusahaan_fk');
        });
        Schema::table('role_ksps', function (Blueprint $table) {
            $table->dropForeign('role_ksps_admin_id_foreign');
        });
        Schema::table('repayments', function (Blueprint $table) {
            $table->dropForeign('repayments_ksp_admin_id_foreign');
        });
        Schema::table('repayments', function (Blueprint $table) {
            $table->dropForeign('repayments_id_invoicing_foreign');
        });
        Schema::table('rekening_tomis', function (Blueprint $table) {
            $table->dropForeign('rekening_tomis_bank_id_foreign');
        });
        Schema::table('rekening_tomis', function (Blueprint $table) {
            $table->dropForeign('rekening_tomis_admins_FK');
        });
        Schema::table('rekening_pandus', function (Blueprint $table) {
            $table->dropForeign('rekening_pandus_updated_by_foreign');
        });
        Schema::table('rekening_pandus', function (Blueprint $table) {
            $table->dropForeign('rekening_pandus_bank_id_foreign');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign('fk_referrals_village');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign('fk_referrals_province');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign('fk_referrals_lender');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign('fk_referrals_district');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign('fk_referrals_city');
        });
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign('fk_referrals_bank');
        });
        Schema::table('privy_pses', function (Blueprint $table) {
            $table->dropForeign('privy_pses_user_id_foreign');
        });
        Schema::table('point_histories', function (Blueprint $table) {
            $table->dropForeign('point_histories_users_FK');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->dropForeign('pinjamen_user_id_foreign');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->dropForeign('pinjamen_list_karyawans_FK');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->dropForeign('pinjamen_lenders_FK');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->dropForeign('pinjamen_kyc_users_FK');
        });
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->dropForeign('pinjamen_id_perusahaan_foreign');
        });
        Schema::table('pinjaman_pending_notifications', function (Blueprint $table) {
            $table->dropForeign('pinjaman_pending_notifications_pinjaman_id_foreign');
        });
        Schema::table('perubahaan_emails', function (Blueprint $table) {
            $table->dropForeign('perubahaan_emails_user_id_foreign');
        });
        Schema::table('partner_companies', function (Blueprint $table) {
            $table->dropForeign('fk_partner_companies_company');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropForeign('list_perusahaans_lenders_FK');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropForeign('list_perusahaans_id_provinsi_foreign');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropForeign('list_perusahaans_id_kecamatan_foreign');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropForeign('list_perusahaans_id_kabupaten_foreign');
        });
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropForeign('list_perusahaans_id_desa_foreign');
        });
        Schema::table('list_karyawans', function (Blueprint $table) {
            $table->dropForeign('list_karyawans_id_list_perusahaan_foreign');
        });
        Schema::table('list_karyawans', function (Blueprint $table) {
            $table->dropForeign('list_karyawans_banks_FK');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->dropForeign('lender_updates_second_approver_id_foreign');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->dropForeign('lender_updates_requester_id_foreign');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->dropForeign('lender_updates_lender_id_foreign');
        });
        Schema::table('lender_updates', function (Blueprint $table) {
            $table->dropForeign('lender_updates_first_approver_id_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_updated_by_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_id_provinsi_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_id_kecamatan_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_id_kabupaten_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_id_desa_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_id_bank_foreign');
        });
        Schema::table('lenders', function (Blueprint $table) {
            $table->dropForeign('lenders_created_by_foreign');
        });
        Schema::table('kyc_users', function (Blueprint $table) {
            $table->dropForeign('kyc_users_user_id_foreign');
        });
        Schema::table('ksp_admins', function (Blueprint $table) {
            $table->dropForeign('ksp_admins_lenders_FK');
        });
        Schema::table('jatuh_tempos', function (Blueprint $table) {
            $table->dropForeign('jatuh_tempos_pinjamen_FK');
        });
        Schema::table('jatuh_tempos', function (Blueprint $table) {
            $table->dropForeign('jatuh_tempos_id_pinjaman_foreign');
        });
        Schema::table('invoicing_pinjamen', function (Blueprint $table) {
            $table->dropForeign('invoicing_pinjamen_list_perusahaans_FK');
        });
        Schema::table('invoicing_pinjamen', function (Blueprint $table) {
            $table->dropForeign('invoicing_pinjamen_id_list_perusahaan_foreign');
        });
        Schema::table('history_invoicing_payments', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_payments_metadata_sku_foreign');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_lender_updates_second_approver_id_foreign');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_lender_updates_requester_id_foreign');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_lender_updates_invoicing_lender_id_foreign');
        });
        Schema::table('history_invoicing_lender_updates', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_lender_updates_first_approver_id_foreign');
        });
        Schema::table('history_invoicing_lenders', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_lenders_lenders_FK');
        });
        Schema::table('history_invoicing_lenders', function (Blueprint $table) {
            $table->dropForeign('history_invoicing_lenders_admins_FK');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->dropForeign('fk_financing_request_vendor_payment_term');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->dropForeign('fk_financing_request_vendor');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->dropForeign('fk_financing_request_client');
        });
        Schema::table('financing_request_invoices', function (Blueprint $table) {
            $table->dropForeign('fk_financing_request_approved_by');
        });
        Schema::table('financing_request_configurations', function (Blueprint $table) {
            $table->dropForeign('financing_request_configurations_id_perusahaan_fk');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->dropForeign('fk_disbursements_admin_id');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->dropForeign('disbursements_pinjaman_id_foreign');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->dropForeign('disbursements_ksp_admin_id_foreign');
        });
        Schema::table('disbursements', function (Blueprint $table) {
            $table->dropForeign('disbursements_bank_id_foreign');
        });
        Schema::table('company_referrals', function (Blueprint $table) {
            $table->dropForeign('fk_company_referrals_referral');
        });
        Schema::table('company_referrals', function (Blueprint $table) {
            $table->dropForeign('fk_company_referrals_company');
        });
        Schema::table('company_feature_referrals', function (Blueprint $table) {
            $table->dropForeign('fk_company_feature_referrals_referral');
        });
        Schema::table('company_feature_referrals', function (Blueprint $table) {
            $table->dropForeign('fk_company_feature_referrals_feature');
        });
        Schema::table('company_feature_referrals', function (Blueprint $table) {
            $table->dropForeign('fk_company_feature_referrals_company');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_village');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_province');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_district');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_city');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_bank');
        });
        Schema::table('amount_pinjamen', function (Blueprint $table) {
            $table->dropForeign('amount_pinjamen_list_perusahaans_FK');
        });
    }
};
