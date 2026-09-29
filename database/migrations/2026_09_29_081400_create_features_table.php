<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id()->comment('Kunci utama fitur');
            $table->string('code', 50)->unique('uq_features_code')->comment('Kode unik fitur');
            $table->string('name', 100)->comment('Nama fitur');
            $table->text('description')->nullable()->comment('Deskripsi fitur');
            $table->enum('status', ['active', 'inactive', 'develop', 'maintenance'])->default('active')->comment('Status fitur');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
