<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verif_emails', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_user')->unsigned()->nullable();
            $table->string('code_rand', 255);
            $table->dateTime('expired');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['id_user'], 'verif_emails_users_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verif_emails');
    }
};
