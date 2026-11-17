<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('lender_id')->unsigned()->nullable()->comment('Referensi lender pemilik referral');
            $table->string('national_id_number', 16)->comment('Nomor Induk Kependudukan (NIK)');
            $table->string('name', 255)->comment('Nama lengkap sesuai KTP');
            $table->string('birth_place', 100)->nullable()->comment('Tempat lahir sesuai KTP');
            $table->date('birth_date')->nullable()->comment('Tanggal lahir sesuai KTP');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Jenis kelamin');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', '-'])->nullable()->comment('Golongan darah');
            $table->text('address')->nullable()->comment('Alamat lengkap sesuai KTP');
            $table->string('rt', 3)->nullable()->comment('Nomor RT');
            $table->string('rw', 3)->nullable()->comment('Nomor RW');
            $table->integer('province_id')->nullable()->comment('Referensi provinsi');
            $table->integer('city_id')->nullable()->comment('Referensi kabupaten atau kota');
            $table->integer('district_id')->nullable()->comment('Referensi kecamatan');
            $table->integer('village_id')->nullable()->comment('Referensi kelurahan atau desa');
            $table->string('postal_code', 10)->nullable()->comment('Kode pos');
            $table->enum('religion', ['islam', 'christian', 'catholic', 'hindu', 'buddhist', 'confucian'])->nullable()->comment('Agama sesuai KTP');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->comment('Status perkawinan');
            $table->string('occupation', 100)->nullable()->comment('Pekerjaan sesuai KTP');
            $table->string('nationality', 50)->default('WNI')->comment('Kewarganegaraan');
            $table->string('id_card_file', 255)->nullable()->comment('Path file atau foto KTP');
            $table->string('phone_number', 30)->nullable()->comment('Nomor telepon atau WhatsApp');
            $table->string('email', 255)->nullable()->comment('Alamat email referral');
            $table->bigInteger('bank_id')->unsigned()->nullable()->comment('Referensi bank');
            $table->string('bank_account_number', 100)->nullable()->comment('Nomor rekening bank');
            $table->string('bank_account_name', 255)->nullable()->comment('Nama pemilik rekening');
            $table->string('bank_book_file', 255)->nullable()->comment('Path file bukti rekening atau buku tabungan');
            $table->tinyInteger('is_active')->default(1)->comment('Status referral: 1 aktif, 0 tidak aktif');
            $table->text('notes')->nullable()->comment('Catatan tambahan referral');
            $table->timestamp('created_at')->useCurrent()->comment('Waktu data dibuat');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('Waktu data terakhir diperbarui');
            $table->timestamp('deleted_at')->nullable()->comment('Waktu data dihapus secara soft delete');
            $table->unique(['national_id_number'], 'uk_referrals_national_id_number');
            $table->index(['name'], 'idx_referrals_name');
            $table->index(['province_id'], 'idx_referrals_province_id');
            $table->index(['city_id'], 'idx_referrals_city_id');
            $table->index(['district_id'], 'idx_referrals_district_id');
            $table->index(['village_id'], 'idx_referrals_village_id');
            $table->index(['bank_id'], 'idx_referrals_bank_id');
            $table->index(['bank_account_number'], 'idx_referrals_bank_account_number');
            $table->index(['is_active'], 'idx_referrals_is_active');
            $table->index(['lender_id'], 'idx_referrals_lender_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
