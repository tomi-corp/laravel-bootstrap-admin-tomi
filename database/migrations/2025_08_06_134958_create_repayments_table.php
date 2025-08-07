<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_invoicing')->unsigned();
            $table->foreign('id_invoicing')->references('id')->on('invoicing_pinjamen');
            $table->bigInteger('ksp_admin_id')->unsigned();
            $table->foreign('ksp_admin_id')->references('id')->on('ksp_admins');
            $table->double('amount_total');
            $table->longText('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
