<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amount_pinjamen', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_perusahaan')->unsigned()->nullable();
            $table->double('iuran_anggota')->nullable();
            $table->double('minimal');
            $table->double('maximal');
            $table->double('maximal_persentase')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_perusahaan'], 'amount_pinjamen_list_perusahaans_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amount_pinjamen');
    }
};
