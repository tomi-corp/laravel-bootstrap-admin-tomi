<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SalaryFinancingController extends Controller
{
    public function index()
    {
        $perusahaans = DB::table('salary_financing_companies')
            ->orderByDesc('id')
            ->get()
            ->map(fn($company) => $this->mapCompany($company));

        $perusahaanAktif = collect($perusahaans)
            ->where('status', 'active')
            ->count();

        $perusahaanTidakAktif = collect($perusahaans)
            ->where('status', 'nonactive')
            ->count();

        $provinsis = collect($perusahaans)
            ->pluck('provinsi')
            ->filter(fn($provinsi) => $provinsi && $provinsi !== '-')
            ->unique()
            ->values()
            ->map(fn($provinsi) => ['provinsi' => $provinsi]);

        return view('admin.salary-financing.index', [
            'perusahaans' => $perusahaans,
            'perusahaanAktif' => $perusahaanAktif,
            'perusahaanTidakAktif' => $perusahaanTidakAktif,
            'provinsis' => $provinsis,
            'totalPengajuan' => collect($perusahaans)->sum('total_pengajuan'),
            'totalOutstanding' => collect($perusahaans)->sum('outstanding'),
        ]);
    }

    private function value(object $row, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($row, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    public function show($id)
    {
        $company = DB::table('salary_financing_companies')
            ->where('id', $id)
            ->first();

        if (!$company) {
            abort(404, 'Data tidak ditemukan');
        }

        $perusahaan = $this->mapCompany($company);
        $details = $this->getSalaryFinancingDetails($id, $perusahaan['nama']);
        $plafonTerpakai = $details->sum('total_pinjaman');

        $perusahaan['details'] = $details;
        $perusahaan['plafon_terpakai'] = $plafonTerpakai;
        $perusahaan['sisa_plafon'] = max(0, ((float) $perusahaan['max_plafon']) - $plafonTerpakai);

        return view('admin.salary-financing.show', compact('perusahaan'));
    }

    public function downloadInvoice($id)
    {
        $response = Http::withBasicAuth(
            config('koperasi.identity'),
            config('koperasi.password')
        )->get(config('koperasi.api_url') . "/api/salary-financing-excel/{$id}");

        if ($response->successful()) {
            $result = $response->json();
            $invoice = $this->mapApiInvoice($result, $id);
            $items = $this->mapApiItems($result['data'] ?? []);

            $pdf = Pdf::loadView('admin.salary-financing.invoice-pdf', [
                'invoice' => $invoice,
                'items' => $items,
            ])->setPaper('a4', 'landscape');

            $filename = 'Invoice-Salary-Financing-' . Str::slug($invoice['nama_perusahaan'] . '-' . $invoice['id']) . '.pdf';

            return $pdf->stream($filename);
        }

        if (!Schema::hasTable('salary_financings')) {
            abort(404, 'Data salary financing tidak ditemukan');
        }

        $detail = DB::table('salary_financings')
            ->where('id', $id)
            ->first();

        if (!$detail) {
            abort(404, 'Invoice salary financing tidak ditemukan');
        }

        $companyName = $this->value($detail, ['nama_perusahaan', 'company_name', 'nama'], '-');
        $invoice = $this->mapSalaryFinancingDetail($detail, $companyName);

        $pdf = Pdf::loadView('admin.salary-financing.invoice-pdf', [
            'invoice' => $invoice,
            'items' => [],
        ])->setPaper('a4', 'landscape');

        $filename = 'Invoice-Salary-Financing-' . Str::slug($invoice['nama_perusahaan'] . '-' . $invoice['id']) . '.pdf';

        return $pdf->stream($filename);
    }

    private function mapCompany(object $company): array
    {
        $status = strtolower((string) $this->value($company, ['status'], 'active'));
        $status = in_array($status, ['inactive', 'non_active', 'nonactive'], true) ? 'nonactive' : 'active';

        return [
            'id' => $this->value($company, ['id']),
            'nama' => $this->value($company, ['nama', 'nama_perusahaan', 'company_name', 'name'], '-'),
            'status' => $status,
            'pic_perusahaan' => $this->value($company, ['pic_perusahaan', 'pic_company', 'company_pic', 'pic'], '-'),
            'pic_internal' => $this->value($company, ['pic_internal', 'internal_pic', 'pic_hrd', 'hrd_pic'], '-'),
            'email_perusahaan' => $this->value($company, ['email_perusahaan', 'company_email', 'email'], '-'),
            'telp' => $this->value($company, ['telp', 'telepon', 'phone', 'phone_number'], '-'),
            'provinsi' => $this->value($company, ['provinsi', 'province'], '-'),
            'kabupaten' => $this->value($company, ['kabupaten', 'kabupaten_kota', 'city', 'kota'], '-'),
            'kecamatan' => $this->value($company, ['kecamatan', 'district'], '-'),
            'desa' => $this->value($company, ['desa', 'kelurahan', 'village'], '-'),
            'alamat_lengkap' => $this->value($company, ['alamat_lengkap', 'address', 'alamat'], '-'),
            'payment_date' => $this->value($company, ['payment_date', 'tanggal_pembayaran'], '-'),
            'h_min_jatuh_tempo' => $this->value($company, ['h_min_jatuh_tempo', 'h_jatuh_tempo', 'due_date_minus'], '-'),
            'nomor_pks' => $this->value($company, ['nomor_pks', 'no_pks', 'pks_number'], '-'),
            'max_plafon' => (float) $this->value($company, ['max_plafon', 'maximum_plafon', 'plafon_max', 'max_limit'], 0),
            'total_pengajuan' => (int) $this->value($company, ['total_pengajuan', 'total_submission', 'submission_count'], 0),
            'outstanding' => (float) $this->value($company, ['outstanding', 'outstanding_amount', 'total_outstanding'], 0),
        ];
    }

    private function getSalaryFinancingDetails(int|string $companyId, string $companyName)
    {
        if (!Schema::hasTable('salary_financings')) {
            return collect();
        }

        $query = DB::table('salary_financings');

        $companyIdColumn = collect([
            'salary_financing_company_id',
            'salary_financing_companies_id',
            'company_id',
            'perusahaan_id',
            'id_perusahaan',
        ])->first(fn($column) => Schema::hasColumn('salary_financings', $column));

        if ($companyIdColumn) {
            $query->where($companyIdColumn, $companyId);
        } else {
            $companyNameColumn = collect([
                'nama_perusahaan',
                'company_name',
                'nama',
            ])->first(fn($column) => Schema::hasColumn('salary_financings', $column));

            if (!$companyNameColumn) {
                return collect();
            }

            $query->where($companyNameColumn, $companyName);
        }

        if (Schema::hasColumn('salary_financings', 'created_at')) {
            $query->orderByDesc('created_at');
        } elseif (Schema::hasColumn('salary_financings', 'id')) {
            $query->orderByDesc('id');
        }

        return $query
            ->get()
            ->map(fn($detail) => $this->mapSalaryFinancingDetail($detail, $companyName));
    }

    private function mapSalaryFinancingDetail(object $detail, string $companyName): array
    {
        $totalPinjaman = (float) $this->value($detail, [
            'total_pinjaman',
            'total_pendapatan',
            'jumlah_pinjaman',
            'nominal',
            'plafon_terpakai',
        ], 0);

        $biayaAdmin = (float) $this->value($detail, [
            'biaya_admin',
            'total_dibayarkan',
            'admin_fee',
        ], $totalPinjaman * 0.0225);

        return [
            'id' => $this->value($detail, ['id']),
            'nama_perusahaan' => $this->value($detail, ['nama_perusahaan', 'company_name', 'nama'], $companyName),
            'nomor_invoice' => $this->value($detail, ['nomor_invoice', 'invoice_number', 'no_invoice']),
            'filename' => $this->value($detail, ['filename', 'file_name', 'nama_file'], '-'),
            'jumlah_peminjam' => (int) $this->value($detail, ['jumlah_peminjam', 'total_peminjam', 'total_karyawan', 'jumlah_karyawan'], 0),
            'total_pinjaman' => $totalPinjaman,
            'biaya_admin' => $biayaAdmin,
            'total_tagihan' => (float) $this->value($detail, ['total_tagihan', 'tagihan', 'total_fee'], $totalPinjaman + $biayaAdmin),
            'created_at' => $this->value($detail, ['created_at', 'tanggal_import', 'tanggal']),
            'status' => strtolower((string) $this->value($detail, ['status', 'status_pembayaran'], 'pending')),
        ];
    }

    private function mapApiInvoice(array $result, int|string $id): array
    {
        $salaryFinancing = $result['salary_financing'] ?? [];
        $items = collect($result['data'] ?? []);

        $totalPendapatan = (float) $items->sum(fn($item) => (float) ($item['jumlah_pendapatan'] ?? 0));
        $biayaAdmin = (float) $items->sum(fn($item) => (float) ($item['biaya_admin'] ?? 0));
        $totalTagihan = (float) $items->sum(fn($item) => (float) ($item['total_tagihan'] ?? 0));

        return [
            'id' => $salaryFinancing['id'] ?? $id,
            'id_perusahaan' => $salaryFinancing['id_perusahaan'] ?? null,
            'nama_perusahaan' => $salaryFinancing['nama_perusahaan'] ?? '-',
            'nomor_invoice' => $salaryFinancing['nomor_invoice'] ?? null,
            'filename' => $salaryFinancing['filename'] ?? '-',
            'jumlah_peminjam' => (int) ($result['total_data'] ?? $items->count()),
            'total_pinjaman' => $totalPendapatan,
            'biaya_admin' => $biayaAdmin,
            'total_tagihan' => $totalTagihan,
            'status' => strtolower((string) ($salaryFinancing['status'] ?? 'pending')),
            'tanggal_bukti_bayar' => $salaryFinancing['tanggal_bukti_bayar'] ?? null,
            'upload_bukti_bayar' => $salaryFinancing['upload_bukti_bayar'] ?? null,
            'tanggal_bukti_transfer' => $salaryFinancing['tanggal_bukti_transfer'] ?? null,
            'upload_bukti_transfer' => $salaryFinancing['upload_bukti_transfer'] ?? null,
            'created_at' => $salaryFinancing['created_at'] ?? null,
            'updated_at' => $salaryFinancing['updated_at'] ?? null,
        ];
    }

    private function mapApiItems(array $rows): array
    {
        return collect($rows)->values()->map(function (array $row, int $index) {
            return [
                'no' => $index + 1,
                'tanggal_pengajuan' => $this->excelDateToNormal($row['tanggal_pengajuan'] ?? null),
                'nama' => $row['nama'] ?? '-',
                'nik' => $row['nik'] ?? '-',
                'nip' => $row['nip'] ?? '-',
                'nama_perusahaan' => $row['nama_perusahaan'] ?? '-',
                'penempatan_kerja' => $row['penempatan_kerja'] ?? '-',
                'jumlah_pendapatan' => (float) ($row['jumlah_pendapatan'] ?? 0),
                'tanggal_pembayaran' => $this->excelDateToNormal($row['tanggal_pembayaran'] ?? null),
                'nama_bank' => $row['nama_bank'] ?? '-',
                'no_rekening' => $row['no_rekening'] ?? '-',
                'nama_pemilik_rekening' => $row['nama_pemilik_rekening'] ?? '-',
                'biaya_admin' => (float) ($row['biaya_admin'] ?? 0),
                'total_tagihan' => (float) ($row['total_tagihan'] ?? 0),
                'total_dibayarkan' => (float) ($row['total_dibayarkan'] ?? 0),
                'total_fee' => (float) ($row['total_fee'] ?? 0),
            ];
        })->toArray();
    }

    private function excelDateToNormal($value): string
    {
        if (empty($value)) {
            return '-';
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->format('d-m-Y');
        }

        return (string) $value;
    }
}
