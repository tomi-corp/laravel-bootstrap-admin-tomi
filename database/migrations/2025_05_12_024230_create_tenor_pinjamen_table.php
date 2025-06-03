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
            $table->integer('tenor');
            $table->double('rate');
            $table->double('biaya_admin');
            $table->double('iuran_anggota');
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
