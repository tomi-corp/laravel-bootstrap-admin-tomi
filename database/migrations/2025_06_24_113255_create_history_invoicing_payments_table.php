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
        Schema::create('history_invoicing_payments', function (Blueprint $table) {
            $table->id();
            $table->longText('payment_id');
            $table->double('amount');
            $table->enum('status', ['SUCCEEDED', 'FAILED']);
            $table->string('metadata_sku');
            $table->foreign('metadata_sku')->references('id_code_perusahaan')->on('list_perusahaans');
            $table->longText('reference_id');
            $table->longText('payment_method_id');
            $table->dateTime('created_trx');
            $table->dateTime('updated_trx');
            $table->longText('payment_request_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_invoicing_payments');
    }
};
