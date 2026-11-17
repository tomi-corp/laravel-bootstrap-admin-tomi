<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_invoicing_lenders', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_lender')->unsigned();
            $table->string('no_invoice', 255)->nullable();
            $table->date('period');
            $table->enum('status', ['lunas', 'belum_lunas'])->nullable();
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->bigInteger('update_by')->unsigned()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_lender'], 'history_invoicing_lenders_lenders_FK');
            $table->index(['update_by'], 'history_invoicing_lenders_admins_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_invoicing_lenders');
    }
};
