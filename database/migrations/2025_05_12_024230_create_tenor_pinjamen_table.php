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
        Schema::create('tenor_pinjamen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_perusahaan')->unsigned();
            $table->foreign('id_perusahaan')->references('id')->on('list_perusahaans');
            $table->integer('tenor');
            $table->double('rate');
            $table->double('biaya_admin');
            $table->double('biaya_layanan');
            $table->double('minimal_nominal');
            $table->double('maximal_nominal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenor_pinjamen');
    }
};
