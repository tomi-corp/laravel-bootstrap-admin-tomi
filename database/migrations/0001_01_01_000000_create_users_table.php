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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nama_lengkap');
            $table->string('nik')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('nohp')->unique();
            $table->string('gauth_id')->nullable();
            $table->string('gauth_type')->nullable();
            $table->date('dob')->nullable();
            $table->double('point')->default(0);
            $table->string('selfie')->nullable();
            $table->string('ktp')->nullable();
            $table->string('stnk')->nullable();


            $table->string('nip')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('jabatan')->nullable();
            $table->double('penghasilan')->nullable();
            $table->string('slip_gaji')->nullable();
            $table->string('selfie_ktp')->nullable();
            $table->enum('verified_privy', [1, 0])->default(0);
            $table->string('id_privy');
            $table->string('enterprise_token_privy');
            $table->string('reference_number_privy');
            $table->string('register_token_privy');
            $table->string('token_firebase');
            $table->json('reason_privy');
            $table->tinyInteger('resend_privy');
            $table->tinyInteger('update_data_privy');
            $table->string('kartu_keluarga');

            $table->timestamp('whatsapp_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
