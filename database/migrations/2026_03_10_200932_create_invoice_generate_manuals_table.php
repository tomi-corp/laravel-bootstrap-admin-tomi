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
        Schema::create('invoice_generate_manuals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('filename');
            $table->integer('jumlah_peminjam');
            $table->double('total_pinjaman');
            $table->double('total_dibayarkan');
            $table->double('total_fee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_generate_manuals');
    }
};
