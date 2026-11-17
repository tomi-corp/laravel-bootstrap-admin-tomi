<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_generate_manuals', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->integer('company_id')->nullable();
            $table->string('nama_perusahaan', 255);
            $table->string('filename', 255);
            $table->integer('jumlah_peminjam');
            $table->double('total_pinjaman');
            $table->double('total_dibayarkan');
            $table->double('total_fee');
            $table->string('status', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_generate_manuals');
    }
};
