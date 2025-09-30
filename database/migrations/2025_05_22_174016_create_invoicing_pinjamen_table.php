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
        Schema::create('invoicing_pinjamen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->foreign('id_list_perusahaan')->references('id')->on('list_perusahaans');
            $table->string('no_invoice')->nullable();
            $table->date('period');
            $table->enum('status', ['lunas', 'belum_lunas']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoicing_pinjamen');
    }
};
