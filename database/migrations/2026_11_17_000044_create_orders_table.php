<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->integer('id');
            $table->integer('user_id');
            $table->integer('product_id');
            $table->integer('variant_id')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('installment_month');
            $table->decimal('monthly_installment', 15, 2);
            $table->string('status', 50)->nullable()->default('pending');
            $table->string('serial_number', 255)->nullable();
            $table->string('imei', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
