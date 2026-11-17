<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_ksps', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('admin_id')->unsigned();
            $table->tinyInteger('dashboard');
            $table->tinyInteger('pinjaman_payrol');
            $table->tinyInteger('disbursment');
            $table->tinyInteger('repayment');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['admin_id'], 'role_ksps_admin_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_ksps');
    }
};
