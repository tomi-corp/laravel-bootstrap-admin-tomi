<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Referral menjadi turunan admin: relasi dipindah dari admins.referral_id ke referrals.admin_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table): void {
            $table->unsignedBigInteger('admin_id')->nullable()->after('id')->comment('Akun admin pemilik referral');
            $table->unique('admin_id', 'uk_referrals_admin_id');
            $table->foreign('admin_id', 'fk_referrals_admin')
                ->references('id')->on('admins')->cascadeOnDelete()->cascadeOnUpdate();
        });

        DB::statement('UPDATE referrals r JOIN admins a ON a.referral_id = r.id SET r.admin_id = a.id');

        Schema::table('admins', function (Blueprint $table): void {
            $table->dropForeign('admins_referral_id_foreign');
            $table->dropIndex('admins_referral_id_index');
            $table->dropColumn('referral_id');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table): void {
            $table->unsignedBigInteger('referral_id')->nullable()->after('lender_id');
            $table->index('referral_id', 'admins_referral_id_index');
            $table->foreign('referral_id', 'admins_referral_id_foreign')
                ->references('id')->on('referrals')->nullOnDelete();
        });

        DB::statement('UPDATE admins a JOIN referrals r ON r.admin_id = a.id SET a.referral_id = r.id');

        Schema::table('referrals', function (Blueprint $table): void {
            $table->dropForeign('fk_referrals_admin');
            $table->dropUnique('uk_referrals_admin_id');
            $table->dropColumn('admin_id');
        });
    }
};
