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
        Schema::create('jatuh_tempos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_pinjaman')->unsigned();
            $table->foreign('id_pinjaman')->references('id')->on('pinjamen');
            $table->integer('pembayaran');
            $table->date('jatuh_tempo');
            $table->enum('status', ['lunas', 'belum']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jatuh_tempos');
    }
};
