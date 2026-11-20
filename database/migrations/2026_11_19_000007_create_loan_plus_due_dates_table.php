<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_plus_due_dates', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loan_id');
            $table->integer('installment_number');
            $table->date('due_date');
            $table->enum('status', ['paid', 'unpaid'])->nullable();
            $table->timestamps();
            $table->index('loan_id', 'loan_plus_due_dates_loan_id_index');
            $table->foreign('loan_id', 'loan_plus_due_dates_loan_id_fk')
                ->references('id')->on('loan_plus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_plus_due_dates');
    }
};
