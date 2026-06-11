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
        Schema::create('gadget_tenors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->unsignedSmallInteger('tenor_months');

            $table->decimal('interest_rate', 8, 2)->default(0);
            $table->decimal('insurance_fee', 15, 2)->default(0);
            $table->decimal('provision_fee', 15, 2)->default(0);
            $table->decimal('admin_fee', 15, 2)->default(0);
            $table->decimal('tomi_fee', 15, 2)->default(0);
            $table->decimal('service_fee', 15, 2)->nullable();

            $table->decimal('minimum_amount', 15, 2)->nullable();
            $table->decimal('maximum_amount', 15, 2)->nullable();

            $table->decimal('professional_fee_1', 15, 2)->default(0);
            $table->decimal('professional_fee_2', 15, 2)->default(0);
            $table->decimal('ksp_fee', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('company_id');
            $table->index('tenor_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gadget_tenors');
    }
};