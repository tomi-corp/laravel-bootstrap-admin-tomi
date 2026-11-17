<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_invoicing_payments', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->longText('payment_id');
            $table->double('amount');
            $table->enum('status', ['SUCCEEDED', 'FAILED']);
            $table->string('metadata_sku', 255);
            $table->longText('reference_id');
            $table->longText('payment_method_id');
            $table->dateTime('created_trx');
            $table->dateTime('updated_trx');
            $table->longText('payment_request_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['metadata_sku'], 'history_invoicing_payments_metadata_sku_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_invoicing_payments');
    }
};
