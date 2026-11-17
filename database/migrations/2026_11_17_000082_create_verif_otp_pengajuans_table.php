<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verif_otp_pengajuans', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('pinjaman_id')->unsigned();
            $table->integer('otp');
            $table->integer('failed');
            $table->dateTime('expired');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['user_id'], 'verif_otp_pengajuans_user_id_foreign');
            $table->index(['pinjaman_id'], 'verif_otp_pengajuans_pinjaman_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verif_otp_pengajuans');
    }
};
