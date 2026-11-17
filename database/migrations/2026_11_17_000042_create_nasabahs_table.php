<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->string('nama_lengkap', 255);
            $table->string('nama_singkat', 255)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir', 255);
            $table->date('tanggal_lahir');
            $table->string('nama_ibu', 255);
            $table->string('no_telp', 255);
            $table->enum('agama', ['Islam', 'Katolik', 'Kristen', 'Buddha', 'Konghucu']);
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D3', 'D4', 'S1', 'S2', 'S3']);
            $table->enum('status_perkawinan', ['Kawin', 'Tidak Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('nama_pasangan', 255)->nullable();
            $table->date('tgl_lahir_pasangan')->nullable();
            $table->text('alamat_rumah');
            $table->bigInteger('provinsi_id')->unsigned();
            $table->bigInteger('kota_id')->unsigned();
            $table->bigInteger('kecamatan_id')->unsigned();
            $table->bigInteger('kelurahan_id')->unsigned();
            $table->string('kode_pos', 5);
            $table->bigInteger('kode_dati_id')->unsigned();
            $table->string('nama_perusahaan', 255)->nullable();
            $table->string('bidang_usaha', 255)->nullable();
            $table->text('alamat_kerja')->nullable();
            $table->string('nip', 255)->nullable();
            $table->enum('status_pegawai', ['Karyawan Tetap', 'Karyawan Tidak Tetap', 'Pensiun']);
            $table->date('tgl_akhir_kontrak')->nullable();
            $table->bigInteger('bank_id')->unsigned();
            $table->string('no_rek', 255);
            $table->string('nama_pemilik_rek', 255);
            $table->string('no_ktp', 255);
            $table->string('no_ktp_pasangan', 255)->nullable();
            $table->string('ktp', 255)->nullable();
            $table->enum('status_ktp', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_ktp', 255)->nullable();
            $table->string('no_npwp', 255);
            $table->string('npwp', 255)->nullable();
            $table->enum('status_npwp', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_npwp', 255)->nullable();
            $table->string('selfie', 255)->nullable();
            $table->enum('status_selfie', ['pending', 'approve', 'reject'])->nullable()->default('pending');
            $table->string('note_selfie', 255)->nullable();
            $table->string('kk', 255)->nullable();
            $table->enum('status_kk', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_kk', 255)->nullable();
            $table->string('id_card', 255)->nullable();
            $table->enum('status_id_card', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_id_card', 255)->nullable();
            $table->string('sk_karyawan', 255)->nullable();
            $table->enum('status_sk_karyawan', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_sk_karyawan', 255)->nullable();
            $table->string('slip_gaji', 255)->nullable();
            $table->enum('status_slip_gaji', ['pending', 'approve', 'reject'])->nullable()->default('pending');
            $table->string('note_slip_gaji', 255)->nullable();
            $table->string('buku_tabungan', 255)->nullable();
            $table->enum('status_buku_tabungan', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_buku_tabungan', 255)->nullable();
            $table->string('mutasi_rekening', 255)->nullable();
            $table->enum('status_mutasi_rekening', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_mutasi_rekening', 255)->nullable();
            $table->string('form_slik', 255)->nullable();
            $table->enum('status_form_slik', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_slik', 255)->nullable();
            $table->string('form_kredit', 255)->nullable();
            $table->enum('status_form_kredit', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_kredit', 255)->nullable();
            $table->string('form_bta', 255)->nullable();
            $table->enum('status_form_bta', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_bta', 255)->nullable();
            $table->string('form_checklist', 255)->nullable();
            $table->enum('status_form_checklist', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('note_form_checklist', 255)->nullable();
            $table->string('foto_akad_kredit', 255)->nullable();
            $table->string('pk_tanda_tangan', 255)->nullable();
            $table->string('video_interview', 255)->nullable();
            $table->enum('status_video_interview', ['pending', 'approve', 'reject'])->nullable();
            $table->string('note_video_interview', 255)->nullable();
            $table->string('video_asuransi', 255)->nullable();
            $table->enum('status_video_asuransi', ['pending', 'approve', 'reject'])->nullable();
            $table->string('note_video_asuransi', 255)->nullable();
            $table->string('produk_kredit', 255)->default('KTA Multiguna');
            $table->enum('tipe_bunga', ['flat', 'efektif']);
            $table->decimal('plafond', 15, 2)->nullable();
            $table->decimal('persetujuan_analyst', 15, 2)->nullable();
            $table->integer('jangka_waktu')->nullable();
            $table->decimal('suku_bunga', 5, 2)->nullable();
            $table->decimal('nilai_angsuran', 15, 2)->nullable();
            $table->decimal('biaya_bank', 15, 2)->nullable();
            $table->decimal('biaya_asuransi', 15, 2)->nullable();
            $table->decimal('biaya_koperasi', 15, 2)->nullable();
            $table->decimal('simpanan_koperasi', 15, 2)->nullable();
            $table->enum('tujuan_penggunaan', ['Konsumsi', 'Modal Kerja', 'Investasi'])->nullable();
            $table->decimal('gaji_bersih', 15, 2)->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->string('bukti_disbursement', 255)->nullable();
            $table->date('tanggal_disbursement')->nullable();
            $table->date('mulai_pinjaman')->nullable();
            $table->date('akhir_pinjaman')->nullable();
            $table->decimal('dbr', 15, 2)->nullable();
            $table->bigInteger('admin_id')->unsigned()->nullable();
            $table->bigInteger('analyst_id')->unsigned()->nullable();
            $table->bigInteger('credam_id')->unsigned()->nullable();
            $table->enum('position', ['admin', 'analyst', 'credam']);
            $table->enum('status', ['pending', 'revisi', 'approve', 'reject']);
            $table->longText('catatan_analyst')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nasabahs');
    }
};
