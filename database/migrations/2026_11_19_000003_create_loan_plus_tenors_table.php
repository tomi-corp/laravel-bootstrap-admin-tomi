<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_plus_tenors', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->integer('tenor');
            $table->double('rate');
            $table->double('insurance_rate');
            $table->double('provision_rate');
            $table->double('admin_fee_rate');
            $table->double('tomi_fee_rate');
            $table->double('service_fee')->nullable();
            $table->double('minimum_amount')->nullable();
            $table->double('maximum_amount')->nullable();
            $table->double('professional_fee_1')->default(0);
            $table->double('professional_fee_2')->default(0);
            $table->double('ksp_fee')->default(0);
            $table->timestamps();
            $table->index('company_id', 'loan_plus_tenors_company_id_index');
            $table->foreign('company_id', 'loan_plus_tenors_company_id_fk')
                ->references('id')->on('list_perusahaans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_plus_tenors');
    }
};
