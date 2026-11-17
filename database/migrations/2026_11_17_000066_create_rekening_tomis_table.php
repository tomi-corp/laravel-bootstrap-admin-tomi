<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekening_tomis', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('bank_id')->unsigned();
            $table->string('nomor_rekening', 255);
            $table->string('atas_nama', 255);
            $table->bigInteger('updated_by')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['bank_id'], 'rekening_tomis_bank_id_foreign');
            $table->index(['updated_by'], 'rekening_tomis_admins_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekening_tomis');
    }
};
