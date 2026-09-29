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
        });
    }

    public function down(): void
    {
        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tomi_dana_referral_2_id');
            $table->dropConstrainedForeignId('tomi_dana_referral_1_id');
        });
    }
};
