<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_lengkap', 255)->nullable();
            $table->string('name', 255);
            $table->string('nik', 255)->nullable();
            $table->string('email', 255);
            $table->string('nohp', 255);
            $table->date('dob')->nullable();
            $table->double('point')->nullable()->default(0);
            $table->enum('verified_privy', ['y', 'n'])->nullable()->default('n');
            $table->string('id_privy', 255)->nullable();
            $table->string('enterprise_token_privy', 255)->nullable();
            $table->string('reference_number_privy', 255)->nullable();
            $table->string('register_token_privy', 255)->nullable();
            $table->string('token_firebase', 255)->nullable();
            $table->longText('reason_privy')->nullable();
            $table->tinyInteger('resend_privy')->nullable();
            $table->tinyInteger('update_data_privy')->nullable();
            $table->string('kartu_keluarga', 255)->nullable();
            $table->string('gauth_id', 255)->nullable();
            $table->string('gauth_type', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('whatsapp_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unique(['email'], 'users_email_unique');
            $table->unique(['nohp'], 'users_username_unique');
            $table->unique(['nohp'], 'users_unique_1');
            $table->unique(['nik'], 'users_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
