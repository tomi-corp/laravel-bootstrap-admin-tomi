<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->integer('company_id')->nullable();
            $table->string('google2fa_secret', 255)->nullable();
            $table->string('username', 255);
            $table->string('pin', 255)->nullable();
            $table->string('email', 255);
            $table->string('password', 255);
            $table->enum('type', ['admin', 'master'])->default('admin');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
