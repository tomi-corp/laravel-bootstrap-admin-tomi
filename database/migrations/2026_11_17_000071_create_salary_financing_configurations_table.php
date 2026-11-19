<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_financing_configurations', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_perusahaan')->unsigned()->nullable();
            $table->integer('tenor');
            $table->double('rate');
            $table->double('asuransi');
            $table->double('provisi');
            $table->double('biaya_admin');
            $table->double('fee_tomi');
            $table->double('biaya_layanan')->nullable();
            $table->double('minimal_nominal')->nullable();
            $table->double('maximal_nominal')->nullable();
            $table->double('profesional_fee_1')->default(0);
            $table->double('profesional_fee_2')->default(0);
            $table->double('ksp')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_perusahaan'], 'salary_financing_configurations_id_perusahaan_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_financing_configurations');
    }
};
