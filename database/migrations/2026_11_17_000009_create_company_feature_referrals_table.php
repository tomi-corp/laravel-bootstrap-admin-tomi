<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_feature_referrals', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned()->comment('ID perusahaan, mengacu ke list_perusahaans.id');
            $table->bigInteger('feature_id')->unsigned()->comment('ID fitur, mengacu ke features.id');
            $table->bigInteger('referral_id')->unsigned()->comment('ID referral, mengacu ke referrals.id');
            $table->timestamp('created_at')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('updated_at')->nullable()->comment('Waktu data terakhir diperbarui');
            $table->timestamp('deleted_at')->nullable()->comment('Waktu data dihapus secara soft delete');
            $table->unique(['company_id', 'feature_id', 'referral_id'], 'uq_company_feature_referrals');
            $table->index(['company_id'], 'idx_company_feature_referrals_company_id');
            $table->index(['feature_id'], 'idx_company_feature_referrals_feature_id');
            $table->index(['referral_id'], 'idx_company_feature_referrals_referral_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_feature_referrals');
    }
};
