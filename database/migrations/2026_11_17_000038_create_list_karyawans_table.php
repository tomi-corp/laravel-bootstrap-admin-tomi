<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_karyawans', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('id_list_perusahaan')->unsigned();
            $table->string('nik', 255);
            $table->string('nip', 255);
            $table->string('whatsapp', 255);
            $table->string('nama_karyawan', 255);
            $table->string('alamat', 255);
            $table->string('divisi', 255)->nullable();
            $table->string('jabatan', 255);
            $table->string('max_plafon', 255);
            $table->string('departement', 255);
            $table->date('tanggal_bergabung');
            $table->enum('status', ['tetap', 'percobaan', 'phk', 'resign']);
            $table->enum('status_sp', ['0', '1', '2', '3']);
            $table->bigInteger('id_nama_bank')->unsigned()->nullable();
            $table->string('nama_rekening', 255)->nullable();
            $table->longText('nomor_bank')->nullable();
            $table->enum('approve_satu', ['pending', 'approve', 'reject']);
            $table->enum('approve_dua', ['pending', 'approve', 'reject']);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['nik'], 'list_karyawans_nik_unique');
            $table->unique(['nip'], 'list_karyawans_nip_unique');
            $table->unique(['whatsapp'], 'list_karyawans_whatsapp_unique');
            $table->index(['id_list_perusahaan'], 'list_karyawans_id_list_perusahaan_foreign');
            $table->index(['id_nama_bank'], 'list_karyawans_banks_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_karyawans');
    }
};
