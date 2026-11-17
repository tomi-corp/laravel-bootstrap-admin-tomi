<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoicing_pinjamen', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->string('no_invoice', 255)->nullable();
            $table->date('period');
            $table->enum('status', ['lunas', 'belum_lunas']);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_list_perusahaan'], 'invoicing_pinjamen_list_perusahaans_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoicing_pinjamen');
    }
};
