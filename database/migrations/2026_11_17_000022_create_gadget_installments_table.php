<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gadget_installments', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->integer('id');
            $table->bigInteger('lender_id')->unsigned()->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('seller_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_variant_id', 100)->nullable();
            $table->string('unit_price', 100)->nullable();
            $table->integer('tenor_months')->nullable();
            $table->decimal('interest_rate', 15, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('monthly_installment', 15, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'cancelled'])->nullable();
            $table->string('notes', 100)->nullable();
            $table->string('transfer_date', 100)->nullable();
            $table->string('transfer_proof', 100)->nullable();
            $table->string('is_transferred', 100)->nullable();
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gadget_installments');
    }
};
