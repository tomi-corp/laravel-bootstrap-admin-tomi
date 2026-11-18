<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_financing_items', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('salary_financing_id');
            $table->date('application_date');
            $table->string('name');
            $table->string('nik', 20);
            $table->string('nip', 50);
            $table->string('work_placement');
            $table->decimal('income_amount', 18, 2)->default(0.00);
            $table->date('payment_date')->nullable();
            $table->unsignedBigInteger('bank_id');
            $table->string('account_number', 50);
            $table->string('account_holder_name');
            $table->timestamps();
            $table->softDeletes();

            $table->index('salary_financing_id', 'idx_sfi_salary_financing_id');
            $table->index('nik', 'idx_sfi_nik');
            $table->index('nip', 'idx_sfi_nip');
            $table->index('bank_id', 'idx_sfi_bank_id');
            $table->index('application_date', 'idx_sfi_application_date');
            $table->index('payment_date', 'idx_sfi_payment_date');
            $table->index('deleted_at', 'idx_sfi_deleted_at');

            $table->foreign('bank_id', 'fk_sfi_bank')
                ->references('id')
                ->on('banks')
                ->onUpdate('cascade');
            $table->foreign('salary_financing_id', 'fk_sfi_salary_financing')
                ->references('id')
                ->on('salary_financings')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_financing_items');
    }
};
