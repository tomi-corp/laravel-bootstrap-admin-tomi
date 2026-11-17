<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ksp_admins', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_lender')->unsigned()->nullable();
            $table->string('nama', 255);
            $table->string('username', 255);
            $table->string('remember_token', 255);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_lender'], 'ksp_admins_lenders_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ksp_admins');
    }
};
