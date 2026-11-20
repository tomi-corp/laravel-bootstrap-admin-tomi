<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Disbursement & repayment kini juga bisa diproses admin internal (tabel admins),
 * yang tidak tercatat di ksp_admins. Pencatatnya disimpan di admin_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repayments', function (Blueprint $table) {
            $table->bigInteger('admin_id')->unsigned()->nullable()->after('ksp_admin_id');
            $table->index(['admin_id'], 'idx_repayments_admin_id');
            $table->foreign(['admin_id'], 'fk_repayments_admin_id')->references(['id'])->on('admins')->onUpdate('cascade');
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->bigInteger('ksp_admin_id')->unsigned()->nullable()->change();
        });

        Schema::table('disbursements', function (Blueprint $table) {
            $table->bigInteger('ksp_admin_id')->unsigned()->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('disbursements', function (Blueprint $table) {
            $table->bigInteger('ksp_admin_id')->unsigned()->nullable(false)->change();
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->bigInteger('ksp_admin_id')->unsigned()->nullable(false)->change();
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->dropForeign('fk_repayments_admin_id');
            $table->dropIndex('idx_repayments_admin_id');
            $table->dropColumn('admin_id');
        });
    }
};
