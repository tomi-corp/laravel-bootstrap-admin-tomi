<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financing_request_configurations', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('lender_id')->unsigned();
            $table->smallInteger('tenor')->unsigned();
            $table->decimal('rate', 5, 2)->default(0.00);
            $table->decimal('asuransi', 5, 2)->default(0.00);
            $table->string('provinsi', 255)->nullable();
            $table->decimal('biaya_admin', 5, 2)->default(0.00);
            $table->decimal('fee_tomi', 5, 2)->default(0.00);
            $table->decimal('profesional_fee_1', 5, 2)->default(0.00);
            $table->decimal('profesional_fee_2', 5, 2)->default(0.00);
            $table->decimal('ksp', 5, 2)->default(0.00);
            $table->decimal('iuran_anggota', 15, 2)->default(0.00);
            $table->decimal('biaya_layanan', 15, 2)->default(0.00);
            $table->decimal('minimal_nominal', 15, 2)->default(0.00);
            $table->decimal('maximal_nominal', 15, 2)->default(0.00);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['company_id'], 'idx_financing_request_config_company');
            $table->index(['lender_id'], 'idx_financing_request_config_lender');
            $table->index(['company_id', 'tenor'], 'idx_financing_request_config_company_tenor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financing_request_configurations');
    }
};
