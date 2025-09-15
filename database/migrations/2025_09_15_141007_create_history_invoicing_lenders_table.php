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
            $table->date('period');
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->foreign('id_list_perusahaan')->references('id')->on('list_perusahaans');
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
