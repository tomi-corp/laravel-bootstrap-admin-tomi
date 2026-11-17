<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->string('code', 50)->comment('Kode unik fitur');
            $table->string('name', 100)->comment('Nama fitur');
            $table->text('description')->nullable()->comment('Deskripsi fitur');
            $table->enum('status', ['active', 'inactive', 'develop', 'maintenance'])->default('active')->comment('Status fitur');
            $table->timestamp('created_at')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('updated_at')->nullable()->comment('Waktu data terakhir diperbarui');
            $table->timestamp('deleted_at')->nullable()->comment('Waktu data dihapus secara soft delete');
            $table->unique(['code'], 'uq_features_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
