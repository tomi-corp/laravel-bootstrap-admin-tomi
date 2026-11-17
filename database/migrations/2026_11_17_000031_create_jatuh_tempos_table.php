<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jatuh_tempos', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_pinjaman')->unsigned();
            $table->integer('pembayaran');
            $table->date('jatuh_tempo');
            $table->enum('status', ['lunas', 'belum'])->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_pinjaman'], 'jatuh_tempos_pinjamen_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jatuh_tempos');
    }
};
