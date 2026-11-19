<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_plus_amounts', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->double('member_contribution')->nullable();
            $table->double('minimum_amount');
            $table->double('maximum_amount');
            $table->double('maximum_percentage')->nullable();
            $table->timestamps();
            $table->index('company_id', 'loan_plus_amounts_company_id_index');
            $table->foreign('company_id', 'loan_plus_amounts_company_id_fk')
                ->references('id')->on('list_perusahaans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_plus_amounts');
    }
};
