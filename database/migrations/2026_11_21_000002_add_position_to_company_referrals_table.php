<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Posisi referral pada perusahaan: 1 = Referal 1, 2 = Referal 2.
 * Satu perusahaan maksimal punya dua referral (satu per posisi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_referrals', function (Blueprint $table): void {
            $table->unsignedTinyInteger('position')->nullable()->after('referral_id')->comment('Posisi referral: 1 = Referal 1, 2 = Referal 2');
            $table->unique(['company_id', 'position'], 'uq_company_referrals_company_position');
        });
    }

    public function down(): void
    {
        Schema::table('company_referrals', function (Blueprint $table): void {
            $table->dropUnique('uq_company_referrals_company_position');
            $table->dropColumn('position');
        });
    }
};
