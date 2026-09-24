<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lender_officers', function (Blueprint $table) {
            $table->id()->comment('ID pengurus koperasi');
            $table->foreignId('lender_id')->comment('ID koperasi/lender tempat pengurus menjabat')->constrained('lenders')->cascadeOnUpdate();
            $table->enum('position', ['chairperson', 'vice_chairperson', 'secretary', 'treasurer', 'supervisor', 'other'])->comment('Jabatan pengurus');
            $table->string('first_name', 100)->comment('Nama depan atau nama tunggal sesuai KTP');
            $table->string('last_name', 100)->nullable()->comment('Nama belakang sesuai KTP');
            $table->char('nik', 16)->comment('Nomor Induk Kependudukan sesuai KTP');
            $table->string('place_of_birth', 100)->nullable()->comment('Tempat lahir sesuai KTP');
            $table->date('date_of_birth')->nullable()->comment('Tanggal lahir sesuai KTP');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Jenis kelamin');
            $table->text('ktp_address')->nullable()->comment('Alamat lengkap sesuai KTP');
            $table->char('rt', 3)->nullable()->comment('Nomor RT sesuai alamat KTP');
            $table->char('rw', 3)->nullable()->comment('Nomor RW sesuai alamat KTP');
            $table->unsignedBigInteger('province_id')->nullable()->comment('ID provinsi sesuai alamat KTP');
            $table->unsignedBigInteger('city_id')->nullable()->comment('ID kabupaten atau kota sesuai alamat KTP');
            $table->unsignedBigInteger('district_id')->nullable()->comment('ID kecamatan sesuai alamat KTP');
            $table->unsignedBigInteger('village_id')->nullable()->comment('ID kelurahan atau desa sesuai alamat KTP');
            $table->char('postal_code', 5)->nullable()->comment('Kode pos alamat KTP');
            $table->string('phone_number', 20)->nullable()->comment('Nomor telepon pengurus');
            $table->string('email', 150)->nullable()->comment('Alamat email pengurus');
            $table->string('ktp_file_path')->nullable()->comment('Path berkas foto atau pindaian KTP');
            $table->string('appointment_reference_number', 100)->nullable()->comment('Nomor surat keputusan atau dokumen pengangkatan');
            $table->date('started_at')->comment('Tanggal mulai menjabat');
            $table->date('ended_at')->nullable()->comment('Tanggal akhir menjabat');
            $table->text('notes')->nullable()->comment('Catatan tambahan tentang pengurus');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lender_id', 'position', 'ended_at'], 'lender_officers_lender_position_index');
            $table->index('nik', 'lender_officers_nik_index');
            $table->index('province_id', 'lender_officers_province_id_index');
            $table->index('city_id', 'lender_officers_city_id_index');
            $table->index('district_id', 'lender_officers_district_id_index');
            $table->index('village_id', 'lender_officers_village_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lender_officers');
    }
};
