<?php

namespace App\Imports\Internal;

use App\Imports\Internal\Concerns\ResolvesWilayah;
use App\Models\Admin;
use App\Models\ListPerusahaan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompanyImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    use ResolvesWilayah;

    private const REQUIRED = [
        'pic_perusahaan' => 'PIC Perusahaan',
        'pic_internal' => 'PIC Internal',
        'nama_perusahaan' => 'Nama Perusahaan',
        'email_perusahaan' => 'Email',
        'telepon' => 'Telepon',
        'nomor_pks' => 'Nomor PKS',
        'provinsi' => 'Provinsi',
        'kabupaten' => 'Kabupaten',
        'kecamatan' => 'Kecamatan',
        'desa' => 'Desa',
        'kodepos' => 'Kode Pos',
        'alamat_lengkap' => 'Alamat Lengkap',
        'tanggal_pembayaran' => 'Tanggal Pembayaran',
    ];

    /** @var array<int, string> */
    private array $errors = [];

    private int $imported = 0;

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

            if ($data['email_perusahaan'] !== '') {
                if (! filter_var($data['email_perusahaan'], FILTER_VALIDATE_EMAIL)) {
                    $rowErrors[] = 'Format email tidak valid';
                } elseif (in_array($data['email_perusahaan'], $emailsInFile, true)) {
                    $rowErrors[] = 'Email duplikat di dalam file';
                } elseif (ListPerusahaan::withTrashed()->where('email_perusahaan', $data['email_perusahaan'])->exists()) {
                    $rowErrors[] = 'Email sudah terdaftar';
                }
                $emailsInFile[] = $data['email_perusahaan'];
            }

            if ($data['telepon'] !== '' && ! ctype_digit($data['telepon'])) {
                $rowErrors[] = 'Telepon harus berupa angka';
            }

            if ($data['nomor_pks'] !== '' && ! ctype_alnum($data['nomor_pks'])) {
                $rowErrors[] = 'Nomor PKS hanya boleh huruf dan angka';
            }

            if ($data['tanggal_pembayaran'] !== '' && (! ctype_digit($data['tanggal_pembayaran']) || (int) $data['tanggal_pembayaran'] < 1 || (int) $data['tanggal_pembayaran'] > 31)) {
                $rowErrors[] = 'Tanggal pembayaran harus angka 1 - 31';
            }

            $admin = $data['pic_internal'] !== '' ? $this->findInternalAdmin($data['pic_internal']) : null;
            if ($data['pic_internal'] !== '' && ! $admin) {
                $rowErrors[] = "PIC internal {$data['pic_internal']} tidak ditemukan atau bukan admin internal";
            }

            $location = $rowErrors ? null : $this->resolveWilayah($data, $rowErrors);

            if ($rowErrors) {
                $this->errors[] = "Baris {$line}: ".implode(', ', $rowErrors);

                continue;
            }

            $prepared[] = [$data, $location, $admin];
        }

        if ($this->errors) {
            return;
        }

        DB::transaction(function () use ($prepared) {
            foreach ($prepared as [$data, $location, $admin]) {
                // Field yang diisi disamakan dengan KspController::addPerusahaan().
                $company = new ListPerusahaan;
                $company->unique_code = $this->generateUniqueCode();
                $company->id_code_perusahaan = $this->generateCode();
                $company->pic_perusahaan = $data['pic_perusahaan'];
                $company->admin_id = $admin->id;
                $company->pic_internal = $admin->username;
                $company->nama = $data['nama_perusahaan'];
                $company->email_perusahaan = $data['email_perusahaan'];
                $company->alamat_lengkap = $data['alamat_lengkap'];
                $company->telp = $data['telepon'];
                $company->payment_date = (int) $data['tanggal_pembayaran'];
                $company->nomor_pks = $data['nomor_pks'];
                $company->id_provinsi = $location['provinsi'];
                $company->id_kabupaten = $location['kabupaten'];
                $company->id_kecamatan = $location['kecamatan'];
                $company->id_desa = $location['desa'];
                $company->save();
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
        $data = [];

        foreach (array_keys(self::REQUIRED) as $key) {
            $data[$key] = trim((string) ($row->get($key) ?? ''));
        }

        $data['email_perusahaan'] = Str::lower($data['email_perusahaan']);
        $data['telepon'] = preg_replace('/\s+/', '', $data['telepon']);

        return $data;
    }

    /**
     * PIC internal dicari dari username atau email admin internal (role admin / super_admin).
     */
    private function findInternalAdmin(string $value): ?Admin
    {
        return Admin::query()
            ->where(fn ($query) => $query->where('username', $value)->orWhere('email', Str::lower($value)))
            ->whereHas('role.instansi', fn ($query) => $query->where('code', 'internal'))
            ->whereHas('role', fn ($query) => $query->whereIn('code', ['admin', 'super_admin']))
            ->first();
    }

    private function generateCode(): string
    {
        do {
            $code = '#TomiKsp-'.Carbon::now()->format('dmY').'-'.rand(1000, 9999);
        } while (ListPerusahaan::withTrashed()->where('id_code_perusahaan', $code)->exists());

        return $code;
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
        } while (ListPerusahaan::withTrashed()->where('unique_code', $code)->exists());

        return $code;
    }
}
