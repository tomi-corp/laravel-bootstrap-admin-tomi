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
        Schema::create('history_invoicing_lenders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_lender')->unsigned();
            $table->foreign('id_lender')->references('id')->on('lenders');
            $table->string('no_invoice')->nullable();
            $table->date('period');
            $table->enum('status', ['lunas', 'belum_lunas']);
            $table->bigInteger('update_by')->unsigned()->nullable();
            $table->foreign('update_by')->references('id')->on('admins');
            $table->datetime('tanggal_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_invoicing_lenders');
    }
};
