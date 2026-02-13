<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id();

            // Identitas
            $table->string('nama_lengkap');
            $table->string('nama_singkat')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nama_ibu');
            $table->string('no_telp');
            $table->enum('agama', ['Islam', 'Katolik', 'Kristen', 'Buddha', 'Konghucu']);
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D3', 'D4', 'S1', 'S2', 'S3']);
            $table->enum('status_perkawinan', ['Kawin', 'Tidak Kawin']);

            // Pasangan
            $table->string('nama_pasangan')->nullable();
            $table->date('tgl_lahir_pasangan')->nullable();

            // Alamat
            $table->text('alamat_rumah');
            $table->bigInteger('provinsi_id')->unsigned();
            $table->bigInteger('kota_id')->unsigned();
            $table->bigInteger('kecamatan_id')->unsigned();
            $table->bigInteger('kelurahan_id')->unsigned();
            $table->string('kode_pos', 5);
            $table->bigInteger('kode_dati_id')->unsigned();

            // Pekerjaan
            $table->string('nama_perusahaan');
            $table->string('bidang_usaha');
            $table->text('alamat_kerja');
            $table->string('nip')->nullable();
            $table->enum('status_pegawai', ['Karyawan Tetap', 'Karyawan Tidak Tetap']);
            $table->date('tgl_akhir_kontrak')->nullable();

            // Bank
            $table->bigInteger('bank_id')->unsigned();
            $table->string('no_rek');
            $table->string('nama_pemilik_rek');

            // Path file (disimpan sebagai string path relatif)
            $table->string('no_ktp');
            $table->string('no_ktp_pasangan')->nullable();

            $table->string('ktp')->nullable();
            $table->enum('status_ktp', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_ktp')->nullable();

            $table->string('no_npwp');
            $table->string('npwp')->nullable();
            $table->enum('status_npwp', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_npwp')->nullable();

            $table->string('selfie')->nullable();
            $table->enum('status_selfie', ['pending', 'approve', 'reject'])->default('pending'); // Diperbaiki: status selfie
            $table->string('note_selfie')->nullable();

            $table->string('kk')->nullable();
            $table->enum('status_kk', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_kk')->nullable();

            $table->string('id_card')->nullable();
            $table->enum('status_id_card', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_id_card')->nullable();

            $table->string('sk_karyawan')->nullable();
            $table->enum('status_sk_karyawan', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_sk_karyawan')->nullable();

            $table->string('slip_gaji')->nullable();
            $table->enum('status_slip_gaji', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_slip_gaji')->nullable();

            $table->string('buku_tabungan')->nullable();
            $table->enum('status_buku_tabungan', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_buku_tabungan')->nullable();

            $table->string('mutasi_rekening')->nullable();
            $table->enum('status_mutasi_rekening', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_mutasi_rekening')->nullable();

            $table->string('form_slik')->nullable();
            $table->enum('status_form_slik', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_slik')->nullable();

            $table->string('form_kredit')->nullable();
            $table->enum('status_form_kredit', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_kredit')->nullable();

            $table->string('form_bta')->nullable();
            $table->enum('status_form_bta', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_bta')->nullable();

            $table->string('form_checklist')->nullable();
            $table->enum('status_form_checklist', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_checklist')->nullable();


            $table->string('foto_akad_kredit')->nullable();
            $table->string('pk_tanda_tangan')->nullable();

            // Kolom tambahan dari form KTA Multiguna
            $table->string('produk_kredit')->default('KTA Multiguna'); // Default value
            $table->decimal('plafond', 15, 2)->nullable();
            $table->integer('jangka_waktu')->nullable(); // dalam bulan
            $table->decimal('suku_bunga', 5, 2)->nullable(); // misal: 1.50%
            $table->decimal('nilai_angsuran', 15, 2)->nullable(); // OUTPUT BY SYSTEM
            $table->decimal('biaya_bank', 15, 2)->nullable();
            $table->decimal('biaya_asuransi', 15, 2)->nullable();
            $table->decimal('biaya_koperasi', 15, 2)->nullable();
            $table->decimal('simpanan_koperasi', 15, 2)->nullable();

            // Dropdown tujuan penggunaan — contoh pilihan umum
            $table->enum('tujuan_penggunaan', [
                'Konsumsi',
                'Modak Kerja',
                'Investasi'
            ])->nullable();

            $table->decimal('gaji_bersih', 15, 2)->nullable(); // Inputan
            $table->date('mulai_pinjaman')->nullable(); // Inputan
            $table->date('akhir_pinjaman')->nullable(); // Inputan

            // DBR dihitung sistem → tetap disimpan sebagai output
            $table->decimal('dbr', 15, 2)->nullable(); // Debt Burden Ratio → output system

            // Relasi
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->bigInteger('analyst_id')->unsigned()->nullable();
            $table->bigInteger('credam_id')->unsigned()->nullable();

            $table->enum('position', ['admin', 'analyst', 'credam']);
            $table->enum('status', ['pending', 'revisi',  'approve', 'reject']);
            $table->longText('catatan_analyst')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nasabahs');
    }
};
