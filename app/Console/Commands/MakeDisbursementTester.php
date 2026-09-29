<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeDisbursementTester extends Command
{
    protected $signature = 'tester:disbursement {--lender-id= : ID lender yang akan dipakai} {--count=1 : Jumlah pinjaman dummy}';

    protected $description = 'Membuat data pinjaman pending + document_privy=y agar muncul di menu lender disbursement.';

    public function handle(): int
    {
        foreach (['lenders', 'list_perusahaans', 'list_karyawans', 'users', 'pinjamen'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Tabel {$table} tidak ditemukan.");

                return self::FAILURE;
            }
        }

        $lenderId = $this->option('lender-id') ?: DB::table('lenders')->value('id');

        if (! $lenderId || ! DB::table('lenders')->where('id', $lenderId)->exists()) {
            $this->error('Lender tidak ditemukan. Jalankan dengan --lender-id=ID yang valid.');

            return self::FAILURE;
        }

        $count = max(1, (int) $this->option('count'));

        DB::transaction(function () use ($lenderId, $count) {
            $bankId = $this->ensureBank();
            $companyId = $this->ensureCompany((int) $lenderId, $bankId);
            $employeeId = $this->ensureEmployee($companyId, $bankId);

            for ($i = 1; $i <= $count; $i++) {
                $userId = $this->ensureUser($i);
                $kycId = $this->ensureKyc($userId);
                $loanId = $this->createLoan((int) $lenderId, $companyId, $employeeId, $userId, $kycId, $i);
                $this->createInstallments($loanId, $companyId);
            }
        });

        $this->info("Berhasil membuat {$count} pinjaman tester untuk lender ID {$lenderId}.");
        $this->line('Buka: '.url('/lender/disbursement'));

        return self::SUCCESS;
    }

    private function ensureBank(): ?int
    {
        if (! Schema::hasTable('banks')) {
            return null;
        }

        $bank = DB::table('banks')->where('code_bank', 'BCA')->first();

        if ($bank) {
            return $bank->id;
        }

        return $this->insert('banks', [
            'code_bank' => 'BCA',
            'description' => 'Bank Central Asia',
        ]);
    }

    private function ensureCompany(int $lenderId, ?int $bankId): int
    {
        $code = '#TOMILENDER-TEST-DISBURSEMENT';
        $company = DB::table('list_perusahaans')->where('id_code_perusahaan', $code)->first();
        $region = $this->regionPayload($lenderId);

        if ($company) {
            DB::table('list_perusahaans')
                ->where('id', $company->id)
                ->update($this->filterColumns('list_perusahaans', [
                    'id_lender' => $lenderId,
                    'payment_date' => 25,
                    'h_min_jatuh_tempo' => 7,
                    'updated_at' => now(),
                ]));

            return $company->id;
        }

        return $this->insert('list_perusahaans', array_merge([
            'id_lender' => $lenderId,
            'id_code_perusahaan' => $code,
            'unique_code' => 'TDIS',
            'nama' => 'PT TEST DISBURSEMENT',
            'pic_perusahaan' => 'PIC TEST',
            'pic_internal' => 'INTERNAL TEST',
            'email_perusahaan' => 'test-disbursement-'.Str::lower(Str::random(6)).'@example.test',
            'alamat_lengkap' => 'Alamat tester disbursement',
            'telp' => '021000000',
            'nomor_pks' => 'PKS/TEST/DISBURSEMENT',
            'payment_date' => 25,
            'h_min_jatuh_tempo' => 7,
            'status' => 'active',
            'active_profesional_fee' => 'nonactive',
            'active_set_pinjaman' => 'nonactive',
            'active_tanggal' => '0',
            'bank' => $bankId,
            'no_rekening' => '1234567890',
            'atas_nama_rekening' => 'PT TEST DISBURSEMENT',
            'max_plafon' => 10000000,
        ], $region));
    }

    private function ensureEmployee(int $companyId, ?int $bankId): int
    {
        $employee = DB::table('list_karyawans')
            ->where('id_list_perusahaan', $companyId)
            ->where('nik', '3174000099990001')
            ->first();

        if ($employee) {
            return $employee->id;
        }

        return $this->insert('list_karyawans', [
            'id_list_perusahaan' => $companyId,
            'nik' => '3174000099990001',
            'nip' => 'TEST-DISB-001',
            'whatsapp' => '6281299990001',
            'nama_karyawan' => 'Tester Disbursement',
            'alamat' => 'Alamat tester',
            'divisi' => 'Finance',
            'jabatan' => 'Staff',
            'max_plafon' => 10000000,
            'departement' => 'Finance',
            'tanggal_bergabung' => now()->subYear()->toDateString(),
            'status' => 'tetap',
            'status_sp' => '0',
            'id_nama_bank' => $bankId,
            'nama_rekening' => 'Tester Disbursement',
            'nomor_bank' => '1234567890',
            'approve_satu' => 'approve',
            'approve_dua' => 'approve',
        ]);
    }

    private function ensureUser(int $index): int
    {
        $email = "tester.disbursement.{$index}@example.test";
        $user = DB::table('users')->where('email', $email)->first();

        if ($user) {
            return $user->id;
        }

        return $this->insert('users', [
            'name' => "Tester Disbursement {$index}",
            'nama_lengkap' => "Tester Disbursement {$index}",
            'nik' => '327300009999'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
            'email' => $email,
            'nohp' => '628139999'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
            'password' => bcrypt('password'),
            'id_privy' => 'TEST-PRIVY-'.$index,
            'enterprise_token_privy' => 'token',
            'reference_number_privy' => 'ref',
            'register_token_privy' => 'register',
            'token_firebase' => 'firebase',
            'reason_privy' => json_encode([]),
            'resend_privy' => 0,
            'update_data_privy' => 0,
            'kartu_keluarga' => 'kk-test.jpg',
        ]);
    }

    private function ensureKyc(int $userId): ?int
    {
        if (! Schema::hasTable('kyc_users')) {
            return null;
        }

        $kyc = DB::table('kyc_users')->where('user_id', $userId)->first();

        if ($kyc) {
            return $kyc->id;
        }

        return $this->insert('kyc_users', [
            'user_id' => $userId,
            'ktp' => 'tester/ktp.jpg',
            'selfie' => 'tester/selfie.jpg',
            'status_ktp' => 'approve',
            'status_selfie' => 'approve',
        ]);
    }

    private function createLoan(int $lenderId, int $companyId, int $employeeId, int $userId, ?int $kycId, int $index): int
    {
        $loanCode = 'TEST-DISB-'.now()->format('YmdHis').'-'.$index;
        $nominal = 5000000;

        return $this->insert('pinjamen', [
            'user_id' => $userId,
            'id_perusahaan' => $companyId,
            'id_karyawan' => $employeeId,
            'id_lender' => $lenderId,
            'id_kyc' => $kycId,
            'id_pinjaman' => $loanCode,
            'nominal' => $nominal,
            'tenor' => 3,
            'rate' => 1.5,
            'asuransi' => 0,
            'provisi' => 0,
            'biaya_admin' => 0,
            'iuran_anggota' => 0,
            'total_biaya_admin' => 0,
            'biaya_admin_ksp' => 0,
            'dpp_adm_ksp' => 0,
            'ppn_adm_ksp' => 0,
            'biaya_admin_pandu' => 0,
            'dpp_adm_pandu' => 0,
            'ppn_adm_pandu' => 0,
            'pendapatan_bunga' => 0,
            'dpp_bunga' => 0,
            'ppn_bunga' => 0,
            'biaya_layanan' => 0,
            'total_angsuran_perbulan' => 1700000,
            'jumlah_yang_diterima' => $nominal,
            'jumlah_asuransi' => 0,
            'jumlah_provisi' => 0,
            'profesional_fee_1' => 0,
            'profesional_fee_2' => 0,
            'jumlah_profesional_1' => 0,
            'jumlah_profesional_2' => 0,
            'ksp_fee' => 0,
            'cost_tomi' => 0,
            'cost_ksp' => 0,
            'description_cost_tomi' => 'Tester disbursement',
            'description_cost_ksp' => 'Tester disbursement',
            'status' => 'pending',
            'status_otp' => 'success',
            'document_privy' => 'y',
            'document_name' => 'tester/pk.pdf',
            'document_token' => Str::random(32),
            'ttd' => 'y',
        ]);
    }

    private function createInstallments(int $loanId, int $companyId): void
    {
        if (! Schema::hasTable('jatuh_tempos')) {
            return;
        }

        $company = DB::table('list_perusahaans')->where('id', $companyId)->first();
        $paymentDate = (int) ($company?->payment_date ?: 25);
        $hMinJatuhTempo = (int) ($company?->h_min_jatuh_tempo ?: 0);
        $baseDate = Carbon::now();

        for ($i = 1; $i <= 3; $i++) {
            $this->insert('jatuh_tempos', [
                'id_pinjaman' => $loanId,
                'pembayaran' => $i,
                'jatuh_tempo' => $this->tanggalJatuhTempoPerusahaan($baseDate, $paymentDate, $hMinJatuhTempo, $i - 1),
                'angsuran_perbulan' => 1700000,
                'total_angsuran_perbulan' => 1700000,
                'status' => 'belum',
            ]);
        }
    }

    private function tanggalJatuhTempoPerusahaan(Carbon $tanggalPengajuan, int $paymentDate, int $hMinJatuhTempo, int $monthOffset): string
    {
        $paymentDay = max(1, min($paymentDate, 31));
        $baseDate = $tanggalPengajuan->copy()->startOfDay();
        $thresholdDay = max(1, $paymentDay - $hMinJatuhTempo);

        $firstDueDate = $this->dateWithCompanyPaymentDay($baseDate, $paymentDay);

        if ((int) $baseDate->format('d') > $thresholdDay) {
            $firstDueDate = $this->dateWithCompanyPaymentDay($baseDate->copy()->addMonthNoOverflow(), $paymentDay);
        }

        return $this->dateWithCompanyPaymentDay($firstDueDate->copy()->addMonthsNoOverflow($monthOffset), $paymentDay)
            ->toDateString();
    }

    private function dateWithCompanyPaymentDay(Carbon $date, int $paymentDay): Carbon
    {
        return $date->copy()->day(min($paymentDay, $date->daysInMonth));
    }

    private function insert(string $table, array $data): int
    {
        $payload = $this->filterColumns($table, $data);
        $now = now();

        if (Schema::hasColumn($table, 'created_at')) {
            $payload['created_at'] = $now;
        }

        if (Schema::hasColumn($table, 'updated_at')) {
            $payload['updated_at'] = $now;
        }

        return (int) DB::table($table)->insertGetId($payload);
    }

    private function regionPayload(int $lenderId): array
    {
        $lender = DB::table('lenders')->where('id', $lenderId)->first();

        return [
            'id_provinsi' => $lender?->id_provinsi ?: $this->firstId('wilayah_provinsi_net'),
            'id_kabupaten' => $lender?->id_kabupaten ?: $this->firstId('wilayah_kabupaten_net'),
            'id_kecamatan' => $lender?->id_kecamatan ?: $this->firstId('wilayah_kecamatan_net'),
            'id_desa' => $lender?->id_desa ?: $this->firstId('wilayah_desa_net'),
        ];
    }

    private function firstId(string $table): ?int
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        return DB::table($table)->value('id');
    }

    private function filterColumns(string $table, array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }
}
