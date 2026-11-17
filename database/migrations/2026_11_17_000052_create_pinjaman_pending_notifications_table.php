<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman_pending_notifications', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('pinjaman_id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['pinjaman_id'], 'pinjaman_pending_notifications_pinjaman_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman_pending_notifications');
    }
};
