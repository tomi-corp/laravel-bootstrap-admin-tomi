<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('referrals')) {
            return;
        }

        Schema::create('referrals', function (Blueprint $table) {
            $table->id()->comment('Kunci utama referral');
            $table->string('national_id_number', 16)->unique('uk_referrals_national_id_number')->comment('Nomor Induk Kependudukan (NIK)');
            $table->string('name')->index('idx_referrals_name')->comment('Nama lengkap sesuai KTP');
            $table->string('birth_place', 100)->nullable()->comment('Tempat lahir sesuai KTP');
            $table->date('birth_date')->nullable()->comment('Tanggal lahir sesuai KTP');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('Jenis kelamin');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', '-'])->nullable()->comment('Golongan darah');
            $table->text('address')->nullable()->comment('Alamat lengkap sesuai KTP');
            $table->string('rt', 3)->nullable()->comment('Nomor RT');
            $table->string('rw', 3)->nullable()->comment('Nomor RW');
            $table->integer('province_id')->nullable()->index('idx_referrals_province_id')->comment('Referensi provinsi');
            $table->integer('city_id')->nullable()->index('idx_referrals_city_id')->comment('Referensi kabupaten atau kota');
            $table->integer('district_id')->nullable()->index('idx_referrals_district_id')->comment('Referensi kecamatan');
            $table->integer('village_id')->nullable()->index('idx_referrals_village_id')->comment('Referensi kelurahan atau desa');
            $table->string('postal_code', 10)->nullable()->comment('Kode pos');
            $table->enum('religion', ['islam', 'christian', 'catholic', 'hindu', 'buddhist', 'confucian'])->nullable()->comment('Agama sesuai KTP');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->comment('Status perkawinan');
            $table->string('occupation', 100)->nullable()->comment('Pekerjaan sesuai KTP');
            $table->string('nationality', 50)->default('WNI')->comment('Kewarganegaraan');
            $table->string('id_card_file')->nullable()->comment('File atau foto KTP');
            $table->string('phone_number', 30)->nullable()->comment('Nomor telepon atau WhatsApp');
            $table->string('email')->nullable()->comment('Alamat email');
            $table->foreignId('bank_id')->nullable()->index('idx_referrals_bank_id')->comment('Referensi bank dari tabel banks');
            $table->string('bank_account_number', 100)->nullable()->index('idx_referrals_bank_account_number')->comment('Nomor rekening bank');
            $table->string('bank_account_name')->nullable()->comment('Nama pemilik rekening');
            $table->string('bank_book_file')->nullable()->comment('File bukti rekening atau buku tabungan');
            $table->boolean('is_active')->default(true)->index('idx_referrals_is_active')->comment('Status referral: 1 aktif, 0 tidak aktif');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
            $table->softDeletes()->comment('Waktu data dihapus secara soft delete');
        });

        if (Schema::hasTable('banks')) {
            Schema::table('referrals', function (Blueprint $table) {
                $table->foreign('bank_id', 'fk_referrals_bank')
                    ->references('id')
                    ->on('banks')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        Schema::table('referrals', function (Blueprint $table) {
            if (Schema::hasTable('wilayah_provinsi_net')) {
                $table->foreign('province_id', 'fk_referrals_province')
                    ->references('id')
                    ->on('wilayah_provinsi_net')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }

            if (Schema::hasTable('wilayah_kabupaten_net')) {
                $table->foreign('city_id', 'fk_referrals_city')
                    ->references('id')
                    ->on('wilayah_kabupaten_net')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }

            if (Schema::hasTable('wilayah_kecamatan_net')) {
                $table->foreign('district_id', 'fk_referrals_district')
                    ->references('id')
                    ->on('wilayah_kecamatan_net')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }

            if (Schema::hasTable('wilayah_desa_net')) {
                $table->foreign('village_id', 'fk_referrals_village')
                    ->references('id')
                    ->on('wilayah_desa_net')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
