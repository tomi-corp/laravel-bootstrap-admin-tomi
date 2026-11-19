<?php

namespace App\Imports\Internal;

use App\Models\bank;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\lender;
use App\Models\provinsi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LenderImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    private const REQUIRED = [
        'pic_lender' => 'PIC Lender',
        'pic_internal' => 'PIC Internal',
        'nama_lender' => 'Nama Lender',
        'email_lender' => 'Email',
        'telepon' => 'Telepon',
        'nomor_pks' => 'Nomor PKS',
        'provinsi' => 'Provinsi',
        'kabupaten' => 'Kabupaten',
        'kecamatan' => 'Kecamatan',
        'desa' => 'Desa',
        'kodepos' => 'Kode Pos',
        'alamat_lengkap' => 'Alamat Lengkap',
        'kode_bank' => 'Kode Bank',
        'nama_rekening' => 'Nama Rekening',
        'no_rekening' => 'Nomor Rekening',
    ];

    /** @var array<int, string> */
    private array $errors = [];

    private int $imported = 0;

    public function __construct(private int $adminId)
    {
    }

    public function collection(Collection $rows): void
    {
        $prepared = [];
        $emailsInFile = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2; // baris 1 adalah header
            $data = $this->normalize($row);
            $rowErrors = [];

            foreach (self::REQUIRED as $key => $label) {
                if ($data[$key] === '') {
                    $rowErrors[] = "{$label} wajib diisi";
                }
            }

            if ($data['email_lender'] !== '') {
                if (! filter_var($data['email_lender'], FILTER_VALIDATE_EMAIL)) {
                    $rowErrors[] = 'Format email tidak valid';
                } elseif (in_array($data['email_lender'], $emailsInFile, true)) {
                    $rowErrors[] = 'Email duplikat di dalam file';
                } elseif (lender::where('email_lender', $data['email_lender'])->exists()) {
                    $rowErrors[] = 'Email sudah terdaftar';
                }
                $emailsInFile[] = $data['email_lender'];
            }

            if ($data['telepon'] !== '' && ! ctype_digit($data['telepon'])) {
                $rowErrors[] = 'Telepon harus berupa angka';
            }

            $location = $rowErrors ? null : $this->resolveLocation($data, $rowErrors);
            $bankId = $data['kode_bank'] !== '' ? bank::where('code_bank', $data['kode_bank'])->value('id') : null;
            if ($data['kode_bank'] !== '' && ! $bankId) {
                $rowErrors[] = "Kode bank {$data['kode_bank']} tidak ditemukan";
            }

            if ($rowErrors) {
                $this->errors[] = "Baris {$line}: ".implode(', ', $rowErrors);

                continue;
            }

            $prepared[] = [$data, $location, $bankId];
        }

        if ($this->errors) {
            return;
        }

        DB::transaction(function () use ($prepared) {
            foreach ($prepared as [$data, $location, $bankId]) {
                lender::create([
                    'id_code_lender' => $this->generateCode(),
                    'pic_lender' => $data['pic_lender'],
                    'pic_internal' => $data['pic_internal'],
                    'nama' => $data['nama_lender'],
                    'email_lender' => $data['email_lender'],
                    'telp' => $data['telepon'],
                    'nomor_pks' => $data['nomor_pks'],
                    'alamat_lengkap' => $data['alamat_lengkap'],
                    'id_provinsi' => $location['provinsi'],
                    'id_kabupaten' => $location['kabupaten'],
                    'id_kecamatan' => $location['kecamatan'],
                    'id_desa' => $location['desa'],
                    // File PKS & logo tidak bisa diimport dari Excel; wajib dilengkapi lewat menu ubah.
                    'file_pks' => '',
                    'last_update_file_pks' => Carbon::now()->format('Y-m-d'),
                    'id_bank' => $bankId,
                    'nama_rekening' => $data['nama_rekening'],
                    'no_rekening' => $data['no_rekening'],
                    'color' => $data['warna'] !== '' ? $data['warna'] : '#188ae2',
                    'status' => 'nonactive',
                    'created_by' => $this->adminId,
                ]);
                $this->imported++;
            }
        });
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function importedCount(): int
    {
        return $this->imported;
    }

    private function normalize(Collection $row): array
    {
        $keys = array_merge(array_keys(self::REQUIRED), ['warna']);
        $data = [];

        foreach ($keys as $key) {
            $data[$key] = trim((string) ($row->get($key) ?? ''));
        }

        $data['email_lender'] = Str::lower($data['email_lender']);
        $data['telepon'] = preg_replace('/\s+/', '', $data['telepon']);
        $data['no_rekening'] = preg_replace('/\s+/', '', $data['no_rekening']);

        return $data;
    }

    private function resolveLocation(array $data, array &$rowErrors): ?array
    {
        $provinsi = provinsi::where('provinsi', $data['provinsi'])->first();
        if (! $provinsi) {
            $rowErrors[] = "Provinsi {$data['provinsi']} tidak ditemukan";

            return null;
        }

        $kabupaten = kota::where('provinsi', $provinsi->provinsi)->where('kabupaten', $data['kabupaten'])->first();
        if (! $kabupaten) {
            $rowErrors[] = "Kabupaten {$data['kabupaten']} tidak ditemukan di provinsi {$data['provinsi']}";

            return null;
        }

        $kecamatan = kecamatan::where('kabupaten', $kabupaten->kabupaten)->where('kecamatan', $data['kecamatan'])->first();
        if (! $kecamatan) {
            $rowErrors[] = "Kecamatan {$data['kecamatan']} tidak ditemukan di {$data['kabupaten']}";

            return null;
        }

        $desa = kelurahan::where('kecamatan', $kecamatan->kecamatan)
            ->where('desa', $data['desa'])
            ->where('kodepos', $data['kodepos'])
            ->first();
        if (! $desa) {
            $rowErrors[] = "Desa {$data['desa']} ({$data['kodepos']}) tidak ditemukan di kecamatan {$data['kecamatan']}";

            return null;
        }

        return [
            'provinsi' => $provinsi->id,
            'kabupaten' => $kabupaten->id,
            'kecamatan' => $kecamatan->id,
            'desa' => $desa->id,
        ];
    }

    private function generateCode(): string
    {
        do {
            $code = '#TomiKspLender-'.Carbon::now()->format('dmY').'-'.rand(1000, 9999);
        } while (lender::where('id_code_lender', $code)->exists());

        return $code;
    }
}
