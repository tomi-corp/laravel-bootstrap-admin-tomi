<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreignId('tomi_dana_referral_1_id')
                ->nullable()
                ->after('active_profesional_fee')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_dana_referral_2_id')
                ->nullable()
                ->after('tomi_dana_referral_1_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_dana_plus_referral_1_id')
                ->nullable()
                ->after('tomi_dana_referral_2_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_dana_plus_referral_2_id')
                ->nullable()
                ->after('tomi_dana_plus_referral_1_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_gawai_referral_1_id')
                ->nullable()
                ->after('tomi_dana_plus_referral_2_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_gawai_referral_2_id')
                ->nullable()
                ->after('tomi_gawai_referral_1_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_gaji_referral_1_id')
                ->nullable()
                ->after('tomi_gawai_referral_2_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_gaji_referral_2_id')
                ->nullable()
                ->after('tomi_gaji_referral_1_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_invoice_referral_1_id')
                ->nullable()
                ->after('tomi_gaji_referral_2_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tomi_invoice_referral_2_id')
                ->nullable()
                ->after('tomi_invoice_referral_1_id')
                ->constrained('referrals')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tomi_dana_referral_2_id');
            $table->dropConstrainedForeignId('tomi_dana_referral_1_id');
            $table->dropConstrainedForeignId('tomi_dana_plus_referral_2_id');
            $table->dropConstrainedForeignId('tomi_dana_plus_referral_1_id');
            $table->dropConstrainedForeignId('tomi_gawai_referral_2_id');
            $table->dropConstrainedForeignId('tomi_gawai_referral_1_id');
            $table->dropConstrainedForeignId('tomi_gaji_referral_2_id');
            $table->dropConstrainedForeignId('tomi_gaji_referral_1_id');
            $table->dropConstrainedForeignId('tomi_invoice_referral_2_id');
            $table->dropConstrainedForeignId('tomi_invoice_referral_1_id');
        });
    }
};
