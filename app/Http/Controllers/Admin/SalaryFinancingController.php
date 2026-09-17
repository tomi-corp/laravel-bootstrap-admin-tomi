<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SalaryFinancingController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $query = $this->companyQuery()
            ->orderByDesc('id');
        $lenderId = $request->integer('lender_id');

        if ($admin->role === 'pic' || $admin->type === 'pic') {
            $query->where('id', $admin->company_id);
        }

        if ($lenderId) {
            $this->applyCompanyLenderFilter($query, $lenderId);
        }

        $perusahaans = $query
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
            ->map(fn($provinsi) => [
                'provinsi' => $provinsi
            ]);

        return view('admin.salary-financing.index', [
            'perusahaans' => $perusahaans,
            'perusahaanAktif' => $perusahaanAktif,
            'perusahaanTidakAktif' => $perusahaanTidakAktif,
            'provinsis' => $provinsis,
            'totalPengajuan' => collect($perusahaans)->sum('total_pengajuan'),
            'totalOutstanding' => collect($perusahaans)->sum('outstanding'),
            'selectedLender' => $lenderId ? $this->findLender($lenderId) : null,
        ]);
    }

    public function companies(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $query = $this->companyQuery()
            ->orderByDesc('id');
        $lenderId = $request->integer('lender_id');

        if ($admin->role === 'pic' || $admin->type === 'pic') {
            $query->where('id', $admin->company_id);
        }

        if ($lenderId) {
            $this->applyCompanyLenderFilter($query, $lenderId);
        }

        return view('admin.salary-financing.companies', [
            'perusahaans' => $query->get()->map(fn($company) => $this->mapCompany($company)),
            'selectedLender' => $lenderId ? $this->findLender($lenderId) : null,
        ]);
    }

    public function lenders()
    {
        return view('admin.salary-financing.lenders', [
            'lenders' => $this->registeredLenders(),
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

    private function companyQuery()
    {
        $query = DB::table('list_perusahaans');

        if (Schema::hasColumn('list_perusahaans', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query;
    }

    public function show($id)
    {
        $company = $this->companyQuery()
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

    public function downloadInvoice(Request $request, $id)
    {
        $allowedColumns = [
            'rate',
            'asuransi',
            'provinsi',
            'biaya_admin',
            'biaya_tomi',
            'biaya_referal_1',
            'biaya_referal_2',
            'ksp',
            'iuran_anggota',
        ];

        $selectedColumns = $request->get('columns', []);
        if (!is_array($selectedColumns)) {
            $selectedColumns = [];
        }

        $selectedColumns = array_values(array_intersect($selectedColumns, $allowedColumns));

        if (empty($selectedColumns) && !$request->has('column_selection')) {
            $selectedColumns = $allowedColumns;
        }

        $response = Http::withBasicAuth(
            config('koperasi.identity'),
            config('koperasi.password')
        )->get(config('koperasi.api_url') . "/api/salary-financing-excel/{$id}");

        if ($response->successful()) {
            $result = $response->json();
            $salaryFinancing = $result['salary_financing'] ?? [];
            $items = $this->mapApiItems($result['data'] ?? [], $salaryFinancing['id_perusahaan'] ?? null);
            $invoice = $this->mapApiInvoice($result, $id, $items);

            $pdf = Pdf::loadView('admin.salary-financing.invoice-pdf', [
                'invoice' => $invoice,
                'items' => $items,
                'selectedColumns' => $selectedColumns,
            ])->setPaper('legal', 'landscape');

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
            'selectedColumns' => $selectedColumns,
        ])->setPaper('legal', 'landscape');

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

    private function registeredLenders()
    {
        if (!Schema::hasTable('lenders')) {
            return collect();
        }

        $query = DB::table('lenders');

        if (Schema::hasColumn('lenders', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query
            ->orderByDesc('id')
            ->get()
            ->map(function (object $lender) {
                $rawStatus = strtolower((string) $this->value($lender, ['status'], 'active'));
                $status = in_array($rawStatus, ['inactive', 'non_active', 'nonactive', '0'], true) ? 'nonactive' : 'active';

                return [
                    'id' => $this->value($lender, ['id']),
                    'kode' => $this->value($lender, ['id_code_lender', 'kode_lender', 'code'], '-'),
                    'nama' => $this->value($lender, ['nama', 'name', 'nama_lender'], '-'),
                    'pic_lender' => $this->value($lender, ['pic_lender', 'pic', 'person_in_charge'], '-'),
                    'email' => $this->value($lender, ['email_lender', 'email'], '-'),
                    'telp' => $this->value($lender, ['telp', 'telepon', 'phone'], '-'),
                    'nomor_pks' => $this->value($lender, ['nomor_pks', 'no_pks', 'pks_number'], '-'),
                    'status' => $status,
                ];
            });
    }

    private function findLender(int $id): ?array
    {
        if (!Schema::hasTable('lenders')) {
            return null;
        }

        $query = DB::table('lenders')->where('id', $id);

        if (Schema::hasColumn('lenders', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $lender = $query->first();

        if (!$lender) {
            return null;
        }

        return [
            'id' => $this->value($lender, ['id']),
            'kode' => $this->value($lender, ['id_code_lender', 'kode_lender', 'code'], '-'),
            'nama' => $this->value($lender, ['nama', 'name', 'nama_lender'], '-'),
        ];
    }

    private function applyCompanyLenderFilter($query, int $lenderId): void
    {
        foreach (['id_lender', 'lender_id'] as $column) {
            if (Schema::hasColumn('list_perusahaans', $column)) {
                $query->where($column, $lenderId);

                return;
            }
        }
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

        $companyId = $this->value($detail, [
            'salary_financing_company_id',
            'salary_financing_companies_id',
            'company_id',
            'perusahaan_id',
            'id_perusahaan',
        ]);
        $tenor = $this->value($detail, ['tenor', 'tenor_bulan']);
        $configuration = $this->getSalaryFinancingConfiguration($companyId, $tenor, $totalPinjaman);

        $biayaAdminRate = (float) $this->value($detail, [
            'biaya_admin_rate',
            'persen_biaya_admin',
            'biaya_admin_persen',
        ], $configuration['biaya_admin'] ?? 0);

        $feeTomiRate = (float) $this->value($detail, [
            'fee_tomi',
            'fee_tomi_rate',
        ], $configuration['fee_tomi'] ?? 0);

        $profesionalFee1Rate = (float) $this->value($detail, [
            'profesional_fee_1',
            'professional_fee_1',
        ], $configuration['profesional_fee_1'] ?? 0);

        $profesionalFee2Rate = (float) $this->value($detail, [
            'profesional_fee_2',
            'professional_fee_2',
        ], $configuration['profesional_fee_2'] ?? 0);

        $biayaAdmin = (float) $this->value($detail, [
            'jumlah_biaya_admin',
            'biaya_admin',
            'admin_fee',
        ], $totalPinjaman * $this->rateToMultiplier($biayaAdminRate));
        $biayaAdmin = $this->roundUpToThousand($biayaAdmin);

        $totalTagihan = $this->roundUpToThousand($totalPinjaman + $biayaAdmin);

        return [
            'id' => $this->value($detail, ['id']),
            'id_perusahaan' => $companyId,
            'nama_perusahaan' => $this->value($detail, ['nama_perusahaan', 'company_name', 'nama'], $companyName),
            'penempatan_kerja' => $this->value($detail, ['penempatan_kerja', 'penempatan', 'lokasi_kerja', 'cabang'], '-'),
            'tanggal_pencairan' => $this->excelDateToNormal($this->value($detail, [
                'tanggal_pencairan',
                'tanggal_pembayaran',
                'tanggal_transfer',
                'tanggal_pengajuan',
                'tanggal_bukti_transfer',
            ])),
            'nomor_invoice' => $this->value($detail, ['nomor_invoice', 'invoice_number', 'no_invoice']),
            'filename' => $this->value($detail, ['filename', 'file_name', 'nama_file'], '-'),
            'jumlah_peminjam' => (int) $this->value($detail, ['jumlah_peminjam', 'total_peminjam', 'total_karyawan', 'jumlah_karyawan'], 0),
            'total_pinjaman' => $totalPinjaman,
            'rate' => (float) $this->value($detail, ['rate'], $configuration['rate'] ?? 0),
            'asuransi' => (float) $this->value($detail, ['asuransi'], $configuration['asuransi'] ?? 0),
            'provinsi' => $this->value($detail, ['provinsi', 'province'], $configuration['provinsi'] ?? '-'),
            'biaya_admin' => $biayaAdmin,
            'biaya_admin_rate' => $biayaAdminRate,
            'fee_tomi' => $feeTomiRate,
            'profesional_fee_1' => $profesionalFee1Rate,
            'profesional_fee_2' => $profesionalFee2Rate,
            'ksp' => (float) $this->value($detail, ['ksp'], $configuration['ksp'] ?? 0),
            'iuran_anggota' => (float) $this->value($detail, ['iuran_anggota'], $configuration['iuran_anggota'] ?? 0),
            'total_tagihan' => $totalTagihan,
            'created_at' => $this->value($detail, ['created_at', 'tanggal_import', 'tanggal']),
            'status' => strtolower((string) $this->value($detail, ['status', 'status_pembayaran'], 'pending')),
        ];
    }

    private function mapApiInvoice(array $result, int|string $id, array $mappedItems = []): array
    {
        $salaryFinancing = $result['salary_financing'] ?? [];
        $items = collect($mappedItems);

        $configuration = $this->getSalaryFinancingConfiguration($salaryFinancing['id_perusahaan'] ?? null);
        $totalPendapatan = (float) $items->sum(fn($item) => (float) ($item['jumlah_pendapatan'] ?? 0));
        $biayaAdmin = (float) $items->sum(fn($item) => (float) ($item['biaya_admin'] ?? 0));
        $totalTagihan = (float) $items->sum(fn($item) => (float) ($item['total_tagihan'] ?? 0));

        return [
            'id' => $salaryFinancing['id'] ?? $id,
            'id_perusahaan' => $salaryFinancing['id_perusahaan'] ?? null,
            'nama_perusahaan' => $salaryFinancing['nama_perusahaan'] ?? '-',
            'penempatan_kerja' => $salaryFinancing['penempatan_kerja'] ?? $salaryFinancing['penempatan'] ?? $salaryFinancing['lokasi_kerja'] ?? $salaryFinancing['cabang'] ?? '-',
            'tanggal_pencairan' => $this->excelDateToNormal(
                $salaryFinancing['tanggal_pencairan']
                ?? $salaryFinancing['tanggal_pembayaran']
                ?? $salaryFinancing['tanggal_transfer']
                ?? $salaryFinancing['tanggal_pengajuan']
                ?? $salaryFinancing['tanggal_bukti_transfer']
                ?? null
            ),
            'nomor_invoice' => $salaryFinancing['nomor_invoice'] ?? null,
            'filename' => $salaryFinancing['filename'] ?? '-',
            'jumlah_peminjam' => (int) ($result['total_data'] ?? $items->count()),
            'total_pinjaman' => $totalPendapatan,
            'rate' => $configuration['rate'] ?? 0,
            'asuransi' => $configuration['asuransi'] ?? 0,
            'provinsi' => $configuration['provinsi'] ?? '-',
            'biaya_admin' => $biayaAdmin,
            'biaya_admin_rate' => $configuration['biaya_admin'] ?? 2.5,
            'fee_tomi' => $configuration['fee_tomi'] ?? 1,
            'profesional_fee_1' => $configuration['profesional_fee_1'] ?? 0,
            'profesional_fee_2' => $configuration['profesional_fee_2'] ?? 0,
            'ksp' => $configuration['ksp'] ?? 0,
            'iuran_anggota' => $configuration['iuran_anggota'] ?? 0,
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

    private function mapApiItems(array $rows, int|string|null $companyId = null): array
    {
        return collect($rows)->values()->map(function (array $row, int $index) use ($companyId) {
            $jumlahPendapatan = (float) ($row['jumlah_pendapatan'] ?? 0);
            $configuration = $this->getSalaryFinancingConfiguration(
                $row['id_perusahaan'] ?? $row['company_id'] ?? $companyId,
                $row['tenor'] ?? $row['tenor_bulan'] ?? null,
                $jumlahPendapatan
            );
            $biayaAdminRate = (float) ($row['biaya_admin_rate'] ?? $row['persen_biaya_admin'] ?? $configuration['biaya_admin'] ?? 2.5);
            $biayaAdmin = (float) ($row['biaya_admin'] ?? 0);

            if ($biayaAdmin <= 0 && $biayaAdminRate > 0) {
                $biayaAdmin = $jumlahPendapatan * $this->rateToMultiplier($biayaAdminRate);
            }

            $biayaAdmin = $this->roundUpToThousand($biayaAdmin);

            return [
                'no' => $index + 1,
                'tanggal_pengajuan' => $this->excelDateToNormal($row['tanggal_pengajuan'] ?? null),
                'tanggal_pencairan' => $this->excelDateToNormal(
                    $row['tanggal_pencairan']
                    ?? $row['tanggal_pembayaran']
                    ?? $row['tanggal_transfer']
                    ?? $row['tanggal_pengajuan']
                    ?? null
                ),
                'nama' => $row['nama'] ?? '-',
                'nik' => $row['nik'] ?? '-',
                'nip' => $row['nip'] ?? '-',
                'nama_perusahaan' => $row['nama_perusahaan'] ?? $row['company_name'] ?? $row['perusahaan'] ?? '-',
                'penempatan_kerja' => $row['penempatan_kerja'] ?? $row['penempatan'] ?? $row['lokasi_kerja'] ?? $row['cabang'] ?? '-',
                'jumlah_pendapatan' => $jumlahPendapatan,
                'rate' => (float) ($row['rate'] ?? $configuration['rate'] ?? 0),
                'asuransi' => (float) ($row['asuransi'] ?? $configuration['asuransi'] ?? 0),
                'provinsi' => $row['provinsi'] ?? $row['province'] ?? $configuration['provinsi'] ?? '-',
                'tanggal_pembayaran' => $this->excelDateToNormal($row['tanggal_pembayaran'] ?? null),
                'nama_bank' => $row['nama_bank'] ?? '-',
                'no_rekening' => $row['no_rekening'] ?? '-',
                'nama_pemilik_rekening' => $row['nama_pemilik_rekening'] ?? '-',
                'biaya_admin' => $biayaAdmin,
                'biaya_admin_rate' => $biayaAdminRate,
                'fee_tomi' => (float) ($row['fee_tomi'] ?? $configuration['fee_tomi'] ?? 1),
                'profesional_fee_1' => (float) ($row['profesional_fee_1'] ?? $row['professional_fee_1'] ?? $configuration['profesional_fee_1'] ?? 0),
                'profesional_fee_2' => (float) ($row['profesional_fee_2'] ?? $row['professional_fee_2'] ?? $configuration['profesional_fee_2'] ?? 0),
                'ksp' => (float) ($row['ksp'] ?? $configuration['ksp'] ?? 0),
                'iuran_anggota' => (float) ($row['iuran_anggota'] ?? $configuration['iuran_anggota'] ?? 0),
                'total_tagihan' => $this->roundUpToThousand($jumlahPendapatan + $biayaAdmin),
                'total_dibayarkan' => (float) ($row['total_dibayarkan'] ?? 0),
                'total_fee' => (float) ($row['total_fee'] ?? 0),
            ];
        })->toArray();
    }

    private function roundUpToThousand(float|int $value): float
    {
        if ($value <= 0) {
            return 0;
        }

        return ceil($value / 1000) * 1000;
    }

    private function rateToMultiplier(float|int $value): float
    {
        if ($value <= 0) {
            return 0;
        }

        return $value <= 1 ? (float) $value : ((float) $value / 100);
    }

    private function getSalaryFinancingConfiguration(int|string|null $companyId, int|string|null $tenor = null, float|int|null $nominal = null): array
    {
        if (empty($companyId) || !Schema::hasTable('salary_financing_configurations')) {
            return [];
        }

        $baseQuery = DB::table('salary_financing_configurations')
            ->where('company_id', $companyId);

        if (Schema::hasColumn('salary_financing_configurations', 'deleted_at')) {
            $baseQuery->whereNull('deleted_at');
        }

        $query = clone $baseQuery;

        if (!empty($tenor) && Schema::hasColumn('salary_financing_configurations', 'tenor')) {
            $query->where('tenor', (int) $tenor);
        }

        if (
            !empty($nominal)
            && Schema::hasColumn('salary_financing_configurations', 'minimal_nominal')
            && Schema::hasColumn('salary_financing_configurations', 'maximal_nominal')
        ) {
            $query->where('minimal_nominal', '<=', $nominal)
                ->where('maximal_nominal', '>=', $nominal);
        }

        $configuration = $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if (!$configuration && !empty($nominal)) {
            $query = clone $baseQuery;

            if (!empty($tenor) && Schema::hasColumn('salary_financing_configurations', 'tenor')) {
                $query->where('tenor', (int) $tenor);
            }

            $configuration = $query
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();
        }

        if (!$configuration && !empty($tenor)) {
            $configuration = (clone $baseQuery)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();
        }

        if (!$configuration) {
            return [];
        }
        return [
            'rate' => (float) ($configuration->rate ?? 0),
            'asuransi' => (float) ($configuration->asuransi ?? 0),
            'provinsi' => $configuration->provinsi ?? '-',
            'biaya_admin' => (float) ($configuration->biaya_admin ?? 0),
            'fee_tomi' => (float) ($configuration->fee_tomi ?? 0),
            'profesional_fee_1' => (float) ($configuration->profesional_fee_1 ?? $configuration->professional_fee_1 ?? 0),
            'profesional_fee_2' => (float) ($configuration->profesional_fee_2 ?? $configuration->professional_fee_2 ?? 0),
            'ksp' => (float) ($configuration->ksp ?? 0),
            'iuran_anggota' => (float) ($configuration->iuran_anggota ?? 0),
        ];
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
