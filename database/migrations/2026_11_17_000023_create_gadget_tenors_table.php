<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gadget_tenors', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->smallInteger('tenor_months')->unsigned();
            $table->decimal('interest_rate', 8, 2)->default(0.00);
            $table->decimal('insurance_fee', 15, 2)->default(0.00);
            $table->decimal('provision_fee', 15, 2)->default(0.00);
            $table->decimal('admin_fee', 15, 2)->default(0.00);
            $table->decimal('tomi_fee', 15, 2)->default(0.00);
            $table->decimal('service_fee', 15, 2)->nullable();
            $table->decimal('minimum_amount', 15, 2)->nullable();
            $table->decimal('maximum_amount', 15, 2)->nullable();
            $table->decimal('professional_fee_1', 15, 2)->default(0.00);
            $table->decimal('professional_fee_2', 15, 2)->default(0.00);
            $table->decimal('ksp_fee', 15, 2)->default(0.00);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gadget_tenors');
    }
};
