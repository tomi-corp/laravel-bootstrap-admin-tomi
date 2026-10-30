<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\AddKonfigurasiRequest;
use App\Http\Requests\Admin\Ksp\AmountPinjamanRequest;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
use App\Http\Resources\Admin\Ksp\DetailPerusahaanResource;
use App\Http\Resources\Admin\Ksp\TenorPinjamanResource;
use App\Exports\TomiDanaEmployeeTemplateExport;
use App\Imports\TomiDanaEmployeeImport;
use App\Models\amount_pinjaman;
use App\Models\disbursement;
use App\Models\invoicing_pinjaman;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\ListPerusahaan;
use App\Models\jatuh_tempo;
use App\Models\list_karyawan;
use App\Models\lender;
use App\Models\bank;
use App\Models\pinjaman;
use App\Models\provinsi;
use App\Models\tenor_pinjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TomiDanaController extends Controller
{
    public function employees(?int $companyId = null)
    {
        if ($companyId === null) {
            return view('admin.tomi-dana.employees.companies');
        }

        return view('admin.tomi-dana.employees.index', [
            'companyId' => $companyId,
            'company' => $companyId ? ListPerusahaan::find($companyId) : null,
        ]);
    }

    public function employeesTable()
    {
        $query = list_karyawan::with('perusahaan')->latest('id');

        if (request()->filled('company_id')) {
            $query->where('id_list_perusahaan', request()->integer('company_id'));
        }

        $keyword = trim((string) request()->input('search.value', ''));
        if ($keyword !== '') {
            $query->where(function ($searchQuery) use ($keyword) {
                $searchQuery->where('nama_karyawan', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%")
                    ->orWhere('nip', 'like', "%{$keyword}%")
                    ->orWhere('whatsapp', 'like', "%{$keyword}%")
                    ->orWhereHas('perusahaan', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"));
            });
        }

        return DataTables::of($query->get())
            ->addIndexColumn()
            ->addColumn('nama_karyawan', fn ($row) => strtoupper($row->nama_karyawan ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('status_label', fn ($row) => '<span class="badge badge-secondary">' . e(strtoupper((string) ($row->status ?? '-'))) . '</span>')
            ->addColumn('action', fn ($row) => '<button type="button" class="btn btn-sm btn-warning mr-1" onclick="editEmployee(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deleteEmployee(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function employeeCompaniesTable()
    {
        return DataTables::eloquent(ListPerusahaan::query()->select(['id', 'nama', 'status'])->latest('id'))
            ->addIndexColumn()
            ->addColumn('status_label', fn ($row) => '<span class="badge badge-' . ($row->status === 'active' ? 'success' : 'secondary') . '">' . e(strtoupper((string) ($row->status ?? '-'))) . '</span>')
            ->addColumn('action', fn ($row) => '<a href="' . route('admin.tomi-dana.employees.company', $row->id) . '" class="btn btn-sm btn-info"><i class="mdi mdi-account-group-outline mr-1"></i> Lihat Karyawan</a>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function storeEmployee(Request $request)
    {
        $data = $this->validateEmployee($request, true);
        $employee = new list_karyawan();
        $employee->fill($data);
        $employee->save();

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil ditambahkan.']);
    }

    public function employeeDetail(int $id)
    {
        return response()->json(['status' => 200, 'data' => list_karyawan::findOrFail($id)]);
    }

    public function updateEmployee(Request $request)
    {
        $employee = list_karyawan::findOrFail($request->integer('id'));
        $data = $this->validateEmployee($request, false);
        $employee->fill($data);
        $employee->save();

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil diubah.']);
    }

    public function deleteEmployee(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:list_karyawans,id']);
        list_karyawan::findOrFail($request->integer('id'))->delete();

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil dihapus.']);
    }

    public function importEmployees(Request $request)
    {
        $data = $request->validate([
            'id_list_perusahaan' => 'required|integer|exists:list_perusahaans,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        Excel::import(new TomiDanaEmployeeImport((int) $data['id_list_perusahaan']), $request->file('file'));

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil diimpor.']);
    }

    public function employeeTemplate()
    {
        return Excel::download(new TomiDanaEmployeeTemplateExport(), 'template-import-karyawan-tomidana.xlsx');
    }

    private function validateEmployee(Request $request, bool $includeCompany = true): array
    {
        $rules = [
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'nik' => 'nullable|string|max:100',
            'nip' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'divisi' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'status_sp' => 'nullable|string|max:50',
            'nomor_bank' => 'nullable|string|max:100',
            'max_plafon' => 'nullable|numeric',
        ];

        if ($includeCompany) {
            $rules['id_list_perusahaan'] = 'required|integer|exists:list_perusahaans,id';
        }

        return $request->validate($rules);
    }

    public function disbursements()
    {
        return view('admin.tomi-dana.disbursements.index');
    }

    public function disbursementsTable()
    {
        $query = pinjaman::with(['user', 'data_karyawan', 'lender', 'perusahaan'])
            ->whereRaw('LOWER(TRIM(status_otp)) = ?', ['success'])
            ->whereRaw('LOWER(TRIM(ttd)) = ?', ['y'])
            ->whereRaw('LOWER(TRIM(status)) = ?', ['pending'])
            ->latest('id');

        $keyword = trim((string) request()->input('search.value', ''));
        if ($keyword !== '') {
            $query->where(function ($searchQuery) use ($keyword) {
                $searchQuery->where('id_pinjaman', 'like', "%{$keyword}%")
                    ->orWhereHas('data_karyawan', fn ($q) => $q->where('nama_karyawan', 'like', "%{$keyword}%"))
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"))
                    ->orWhereHas('perusahaan', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"));
            });
        }

        return DataTables::of($query->get())
            ->addIndexColumn()
            ->addColumn('nama_peminjam', fn ($row) => strtoupper($row->data_karyawan?->nama_karyawan ?? $row->user?->name ?? '-'))
            ->addColumn('nomor_pinjaman', fn ($row) => strtolower(ltrim((string) ($row->id_pinjaman ?? '-'), '#')))
            ->addColumn('lender', fn ($row) => strtoupper($row->lender?->nama ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('nominal', fn ($row) => 'Rp. ' . number_format((float) ($row->nominal ?? 0), 0, ',', '.'))
            ->addColumn('tenor_label', fn ($row) => ($row->tenor ?? 0) . ' bulan')
            ->addColumn('tanggal_pengajuan', fn ($row) => $row->created_at ? Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-')
            ->addColumn('evidence', function ($row) {
                $buttons = [];
                foreach (['tte' => ['TTE', 'file-pdf-box'], 'selfie' => ['Selfie', 'camera-outline'], 'ktp' => ['KTP', 'card-account-details-outline']] as $type => [$label, $icon]) {
                    $buttons[] = '<a href="' . route('admin.tomi-dana.disbursements.evidence', [$row->id, $type]) . '" target="_blank" class="btn btn-sm btn-outline-info mr-1 mb-1" title="Lihat ' . $label . '"><i class="mdi mdi-' . $icon . ' mr-1"></i>' . $label . '</a>';
                }

                return implode('', $buttons);
            })
            ->addColumn('action', function ($row) {
                $eligible = strtolower((string) $row->status_otp) === 'success'
                    && strtolower((string) $row->ttd) === 'y'
                    && strtolower((string) $row->status) === 'pending';

                return $eligible
                    ? '<button type="button" class="btn btn-sm btn-success" onclick="approveDisbursement(' . $row->id . ')"><i class="mdi mdi-check-circle-outline mr-1"></i>Approve</button>'
                    : '<button type="button" class="btn btn-sm btn-secondary" disabled title="Belum memenuhi syarat">Tidak tersedia</button>';
            })
            ->rawColumns(['evidence', 'action'])
            ->toJson();
    }

    public function disbursementEvidence(int $id, string $type)
    {
        abort_unless(in_array($type, ['tte', 'selfie', 'ktp'], true), 404);

        $loan = pinjaman::with(['user.kyc', 'user.privy'])->findOrFail($id);

        $path = match ($type) {
            'tte' => $this->evidencePath('assets/pk', $loan->document_name),
            'ktp' => $this->evidencePath('assets/users', $loan->user?->privy?->local_ktp_path)
                ?? $this->evidencePath('assets/users', $loan->user?->kyc?->ktp),
            'selfie' => $this->evidencePath('assets/users', $loan->user?->privy?->local_selfie_path)
                ?? $this->evidencePath('assets/users', $loan->user?->kyc?->selfie),
        };

        abort_if(blank($path), 404, 'Bukti dokumen tidak tersedia.');

        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404, 'Bukti dokumen tidak ditemukan.');

        return response()->file($disk->path($path), [
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    private function evidencePath(string $directory, ?string $fileName): ?string
    {
        return filled($fileName) ? $directory . '/' . basename($fileName) : null;
    }

    public function approveDisbursement(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'transfer_date' => 'required|date',
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $loan = pinjaman::where('id', $request->integer('id'))
            ->whereRaw('LOWER(TRIM(status_otp)) = ?', ['success'])
            ->whereRaw('LOWER(TRIM(ttd)) = ?', ['y'])
            ->whereRaw('LOWER(TRIM(status)) = ?', ['pending'])
            ->first();

        if (! $loan) {
            return response()->json([
                'status' => 422,
                'message' => 'Pinjaman tidak memenuhi syarat untuk di-approve.',
            ], 422);
        }

        $proof = $request->file('transfer_proof');
        $proofName = 'transfer-proof-' . $loan->id . '-' . time() . '-' . Str::random(8) . '.' . $proof->getClientOriginalExtension();
        $proofPath = $proof->storeAs('assets/disbursements', $proofName);

        DB::transaction(function () use ($loan, $request, $proofPath) {
            $disbursement = disbursement::where('pinjaman_id', $loan->id)->first() ?? new disbursement();
            $disbursement->pinjaman_id = $loan->id;
            $disbursement->transfer_date = $request->input('transfer_date');
            $disbursement->transfer_proof = $proofPath;
            $disbursement->save();

            $loan->status = 'approve';
            $loan->save();
        });

        return response()->json([
            'status' => 200,
            'message' => 'Pinjaman berhasil di-approve.',
        ]);
    }

    public function repayments(Request $request, ?int $companyId = null)
    {
        if ($companyId === null) {
            $companies = ListPerusahaan::query()
                ->orderBy('nama')
                ->get(['id', 'id_code_perusahaan', 'nama', 'status']);

            $invoices = invoicing_pinjaman::with([
                'pinjaman' => function ($query) {
                    $query->whereIn('status', ['approve', 'done'])->with('angsuran');
                },
            ])
                ->whereIn('id_list_perusahaan', $companies->pluck('id'))
                ->orderByDesc('period')
                ->get()
                ->groupBy('id_list_perusahaan');

            $companyRows = $companies->flatMap(function ($company) use ($invoices) {
                $companyInvoices = $invoices->get($company->id, collect());

                if ($companyInvoices->isEmpty()) {
                    return [(object) [
                        'id' => $company->id,
                        'id_code_perusahaan' => $company->id_code_perusahaan,
                        'nama' => $company->nama,
                        'status' => $company->status,
                        'jatuh_tempo' => '-',
                        'jumlah_pinjaman' => 0,
                        'total_tagihan' => 0,
                        'invoice' => '-',
                        'status_invoice' => '-',
                    ]];
                }

                return $companyInvoices->map(function ($invoice) use ($company) {
                    $invoicePeriod = $invoice->period
                        ? Carbon::parse($invoice->period)
                        : Carbon::createFromDate($invoice->tahun, $invoice->bulan, 1);
                    $targetPeriod = $invoicePeriod->format('m-Y');
                    $loans = $invoice->pinjaman->filter(function ($loan) use ($targetPeriod) {
                        return $loan->angsuran->contains(fn ($installment) =>
                            $installment->jatuh_tempo
                            && Carbon::parse($installment->jatuh_tempo)->format('m-Y') === $targetPeriod
                        );
                    });

                    return (object) [
                        'id' => $company->id,
                        'id_code_perusahaan' => $company->id_code_perusahaan,
                        'nama' => $company->nama,
                        'status' => $company->status,
                        'jatuh_tempo' => $invoicePeriod->format('d/m/Y'),
                        'jumlah_pinjaman' => $loans->count(),
                        'total_tagihan' => $loans->sum('total_angsuran_perbulan'),
                        'invoice' => 'INV-' . strtoupper($invoicePeriod->format('M')) . '-' . $company->id_code_perusahaan . '-' . $invoice->id,
                        'status_invoice' => strtolower((string) $invoice->status) === 'lunas' ? 'LUNAS' : 'BELUM LUNAS',
                    ];
                });
            })->values();

            return view('admin.tomi-dana.repayments.index', [
                'companies' => $companyRows,
                'company' => null,
                'companyId' => null,
            ]);
        }

        $company = ListPerusahaan::findOrFail($companyId);

        $request->validate([
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:lunas,pending,terlambat',
            'period' => 'nullable|date_format:Y-m',
        ]);

        $query = jatuh_tempo::with(['pinjaman.data_karyawan', 'pinjaman.perusahaan', 'pinjaman.user'])
            ->whereHas('pinjaman', function ($loanQuery) {
                $loanQuery->where('status_otp', 'success');
            })
            ->whereHas('pinjaman', function ($loanQuery) use ($companyId) {
                $loanQuery->where('id_perusahaan', $companyId);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->whereHas('pinjaman', function ($loanQuery) use ($search) {
                    $loanQuery->where('id', 'like', "%{$search}%")
                        ->orWhereHas('data_karyawan', function ($borrowerQuery) use ($search) {
                            $borrowerQuery->where('nama_karyawan', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('period'), function ($query) use ($request) {
                $query->whereYear('jatuh_tempo', substr($request->input('period'), 0, 4))
                    ->whereMonth('jatuh_tempo', substr($request->input('period'), 5, 2));
            })
            ->orderBy('jatuh_tempo')
            ->orderBy('id_pinjaman');

        $repayments = $query->get()->map(function ($installment) {
            $dueDate = $installment->jatuh_tempo ? Carbon::parse($installment->jatuh_tempo) : null;
            $isPaid = strtolower((string) $installment->status) === 'lunas';
            $status = $isPaid ? 'lunas' : ($dueDate?->isBefore(Carbon::today()) ? 'terlambat' : 'pending');

            return [
                'borrower_name' => $installment->pinjaman?->data_karyawan?->nama_karyawan ?? $installment->pinjaman?->user?->name ?? '-',
                'loan_number' => 'TD-' . str_pad((string) ($installment->id_pinjaman ?? '-'), 6, '0', STR_PAD_LEFT),
                'installment_number' => $installment->pembayaran ?? '-',
                'due_date' => $dueDate?->format('d/m/Y') ?? '-',
                'amount' => (float) ($installment->pinjaman?->total_angsuran_perbulan ?? 0),
                'status' => $status,
            ];
        })->filter(function ($repayment) use ($request) {
            return ! $request->filled('status') || $repayment['status'] === $request->input('status');
        })->values();

        return view('admin.tomi-dana.repayments.index', [
            'companies' => collect(),
            'company' => $company,
            'companyId' => $companyId,
            'repayments' => $repayments,
            'totalRepayments' => $repayments->count(),
            'paidRepayments' => $repayments->where('status', 'lunas')->count(),
            'pendingRepayments' => $repayments->where('status', 'pending')->count(),
            'overdueRepayments' => $repayments->where('status', 'terlambat')->count(),
        ]);
    }

    public function repaymentsTable(Request $request)
    {
        $today = Carbon::today();
        $query = jatuh_tempo::with(['pinjaman.data_karyawan', 'pinjaman.user', 'pinjaman.lender', 'pinjaman.perusahaan'])
            ->whereHas('pinjaman', function ($loanQuery) {
                $loanQuery->where('status_otp', 'success');
            })
            ->when($request->filled('status'), function ($query) use ($request, $today) {
                $status = $request->input('status');

                if ($status === 'lunas') {
                    $query->where('status', 'lunas');
                } elseif ($status === 'terlambat') {
                    $query->where('status', '!=', 'lunas')->whereDate('jatuh_tempo', '<', $today);
                } elseif ($status === 'pending') {
                    $query->where('status', '!=', 'lunas')->where(function ($pendingQuery) use ($today) {
                        $pendingQuery->whereDate('jatuh_tempo', '>=', $today)
                            ->orWhereNull('jatuh_tempo');
                    });
                }
            })
            ->when($request->filled('period'), function ($query) use ($request) {
                $query->whereYear('jatuh_tempo', substr($request->input('period'), 0, 4))
                    ->whereMonth('jatuh_tempo', substr($request->input('period'), 5, 2));
            })
            ->when($request->filled('company_id'), function ($query) use ($request) {
                $query->whereHas('pinjaman', function ($loanQuery) use ($request) {
                    $loanQuery->where('id_perusahaan', $request->integer('company_id'));
                });
            });

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('nama_peminjam', function ($row) {
                return strtoupper($row->pinjaman?->data_karyawan?->nama_karyawan ?? $row->pinjaman?->user?->name ?? '-');
            })
            ->addColumn('nomor_pinjaman', function ($row) {
                return strtolower(ltrim((string) ($row->pinjaman?->id_pinjaman ?? '-'), '#'));
            })
            ->addColumn('lender', fn ($row) => strtoupper($row->pinjaman?->lender?->nama ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->pinjaman?->perusahaan?->nama ?? '-'))
            ->addColumn('angsuran_ke', fn ($row) => $row->pembayaran ?? '-')
            ->addColumn('periode', fn ($row) => $row->jatuh_tempo ? Carbon::parse($row->jatuh_tempo)->format('m/Y') : '-')
            ->addColumn('tanggal_jatuh_tempo', fn ($row) => $row->jatuh_tempo ? Carbon::parse($row->jatuh_tempo)->format('d/m/Y') : '-')
            ->addColumn('nominal', fn ($row) => 'Rp. ' . number_format((float) ($row->pinjaman?->total_angsuran_perbulan ?? 0), 0, ',', '.'))
            ->addColumn('status_label', function ($row) use ($today) {
                $isPaid = strtolower((string) $row->status) === 'lunas';
                $isOverdue = ! $isPaid && $row->jatuh_tempo && Carbon::parse($row->jatuh_tempo)->isBefore($today);
                $status = $isPaid ? 'LUNAS' : ($isOverdue ? 'TERLAMBAT' : 'PENDING');
                $badge = $isPaid ? 'success' : ($isOverdue ? 'danger' : 'warning');

                return '<span class="badge badge-' . $badge . '">' . $status . '</span>';
            })
            ->addColumn('action', fn () => '<button type="button" class="btn btn-sm btn-light" title="Detail" disabled><i class="mdi mdi-eye-outline"></i></button>')
            ->filterColumn('nama_peminjam', function ($query, $keyword) {
                $query->whereHas('pinjaman.data_karyawan', function ($borrowerQuery) use ($keyword) {
                    $borrowerQuery->where('nama_karyawan', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('nomor_pinjaman', function ($query, $keyword) {
                $query->whereHas('pinjaman', function ($loanQuery) use ($keyword) {
                    $loanQuery->where('id_pinjaman', 'like', '%' . ltrim($keyword, '#') . '%');
                });
            })
            ->filterColumn('lender', function ($query, $keyword) {
                $query->whereHas('pinjaman.lender', function ($lenderQuery) use ($keyword) {
                    $lenderQuery->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('perusahaan', function ($query, $keyword) {
                $query->whereHas('pinjaman.perusahaan', function ($companyQuery) use ($keyword) {
                    $companyQuery->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function configurations()
    {
        $perusahaans = ListPerusahaan::with('lender')
            ->orderBy('nama')
            ->get();

        return view('admin.tomi-dana.configurations.companies', compact('perusahaans'));
    }

    public function configuration($id)
    {
        $companyId = decryptSecure($id);
        $list_perusahaan = ListPerusahaan::with('amount_pinjaman')->findOrFail($companyId);
        $max_plafon = list_karyawan::where('id_list_perusahaan', $list_perusahaan->id)->max('max_plafon') ?? 0;

        return view('admin.tomi-dana.configurations.index', compact('list_perusahaan', 'max_plafon'));
    }

    public function configurationTable(Request $request)
    {
        $tenor = tenor_pinjaman::where('id_perusahaan', $request->id)->orderBy('tenor')->get();
        $amount = amount_pinjaman::where('id_perusahaan', $request->id)->first();

        return DataTables::of($tenor)
            ->addIndexColumn()
            ->addColumn('tenor', fn ($row) => $row->tenor . ' bulan')
            ->addColumn('rate', fn ($row) => $row->rate . ' %')
            ->addColumn('asuransi', fn ($row) => $row->asuransi . ' %')
            ->addColumn('provisi', fn ($row) => $row->provisi . ' %')
            ->addColumn('biaya_admin', fn ($row) => $row->biaya_admin . ' %')
            ->addColumn('fee_tomi', fn ($row) => $row->fee_tomi . ' %')
            ->addColumn('profesional_fee_1', fn ($row) => $row->profesional_fee_1 . ' %')
            ->addColumn('profesional_fee_2', fn ($row) => $row->profesional_fee_2 . ' %')
            ->addColumn('ksp', fn ($row) => $row->ksp . ' %')
            ->addColumn('iuran_anggota', fn ($row) => 'Rp. ' . number_format($row->tenor * ($amount?->iuran_anggota ?? 0), 0, ',', '.'))
            ->addColumn('biaya_layanan', fn ($row) => 'Rp. ' . number_format($row->biaya_layanan, 0, ',', '.'))
            ->addColumn('minimal_nominal', fn ($row) => 'Rp. ' . number_format($row->minimal_nominal, 0, ',', '.'))
            ->addColumn('maximal_nominal', fn ($row) => 'Rp. ' . number_format($row->maximal_nominal, 0, ',', '.'))
            ->addColumn('action', fn ($row) => '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button class="btn btn-sm btn-danger" onclick="deleteKonfigurasi(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['action'])
            ->toJson();
    }

    public function addConfiguration(AddKonfigurasiRequest $request)
    {
        $tenor = new tenor_pinjaman();
        $this->fillConfiguration($tenor, $request);
        $tenor->id_perusahaan = $request->id;
        $tenor->save();

        return response()->json(['status' => 200, 'message' => 'Konfigurasi tenor berhasil ditambahkan.']);
    }

    public function detailConfiguration(Request $request)
    {
        return response()->json([
            'status' => 200,
            'data' => new TenorPinjamanResource(tenor_pinjaman::findOrFail($request->id)),
        ]);
    }

    public function updateConfiguration(AddKonfigurasiRequest $request)
    {
        $tenor = tenor_pinjaman::findOrFail($request->id);
        $this->fillConfiguration($tenor, $request);
        $tenor->save();

        return response()->json(['status' => 200, 'message' => 'Konfigurasi tenor berhasil diubah.']);
    }

    public function deleteConfiguration(Request $request)
    {
        $request->validate(['id' => 'required|exists:tenor_pinjaman,id']);
        tenor_pinjaman::findOrFail($request->id)->delete();

        return response()->json(['status' => 200, 'message' => 'Konfigurasi tenor berhasil dihapus.']);
    }

    public function updateAmount(AmountPinjamanRequest $request)
    {
        $amount = amount_pinjaman::firstOrNew(['id_perusahaan' => $request->id]);
        $amount->iuran_anggota = $request->iuran_anggota;
        $amount->minimal = $request->minimal;
        $amount->maximal = $request->maximal;
        $amount->maximal_persentase = $request->maximal_persentase;
        $amount->save();

        return response()->json(['status' => 200, 'message' => 'Amount pinjaman berhasil diperbarui.']);
    }

    private function fillConfiguration(tenor_pinjaman $tenor, AddKonfigurasiRequest $request): void
    {
        $tenor->tenor = $request->tenor;
        $tenor->rate = $request->rate;
        $tenor->asuransi = $request->asuransi;
        $tenor->provisi = $request->provisi;
        $tenor->biaya_admin = $request->biaya_admin;
        $tenor->fee_tomi = $request->fee_tomi;
        $tenor->biaya_layanan = $request->biaya_layanan;
        $tenor->minimal_nominal = $request->minimal_nominal;
        $tenor->maximal_nominal = $request->maximal_nominal;
        $tenor->profesional_fee_1 = $request->profesional_fee_1;
        $tenor->profesional_fee_2 = $request->profesional_fee_2;
        $tenor->ksp = $request->ksp;
    }

    public function lenders()
    {
        return view('admin.tomi-dana.lender.index', [
            'banks' => bank::orderBy('code_bank')->get(),
        ]);
    }

    public function lendersTable()
    {
        return DataTables::eloquent(lender::with('bank')->latest())
            ->addIndexColumn()
            ->addColumn('bank', fn ($row) => $row->bank?->code_bank ?? '-')
            ->addColumn('status_label', function ($row) {
                return $row->status === 'active'
                    ? '<span class="badge badge-success">ACTIVE</span>'
                    : '<span class="badge badge-warning">INACTIVE</span>';
            })
            ->addColumn('action', fn ($row) => '<a href="' . route('admin.tomi-dana.lender.pinjaman', ['lender_id' => $row->id]) . '" class="btn btn-sm btn-info mr-1" title="Lihat pinjaman"><i class="mdi mdi-format-list-bulleted"></i></a><button type="button" class="btn btn-sm btn-primary mr-1" onclick="editLender(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deleteLender(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function lenderLoans(Request $request)
    {
        $lender = $request->filled('lender_id')
            ? lender::find($request->integer('lender_id'))
            : null;
        return view('admin.tomi-dana.lender.pinjaman', compact('lender'));
    }

    public function lenderLoansTable(Request $request)
    {
        $monthExpression = "DATE_FORMAT(created_at, '%Y-%m')";
        $query = pinjaman::query()
            ->select([
                DB::raw("{$monthExpression} as bulan"),
                DB::raw('COUNT(*) as total_pinjaman'),
                DB::raw('SUM(nominal) as total_nominal'),
            ]);

        $this->filterLenderLoans($query, $request);

        $query->groupBy(DB::raw($monthExpression))
            ->orderByDesc('bulan');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('bulan_label', fn ($row) => Carbon::createFromFormat('Y-m', $row->bulan)->locale('id')->translatedFormat('F Y'))
            ->editColumn('total_pinjaman', fn ($row) => number_format((int) $row->total_pinjaman, 0, ',', '.'))
            ->addColumn('total_nominal', fn ($row) => 'Rp. ' . number_format((float) $row->total_nominal, 0, ',', '.'))
            ->addColumn('action', function ($row) use ($request) {
                $detailButton = '<button type="button" class="btn btn-sm btn-info mr-1" onclick="showBorrowers(\'' . e($row->bulan) . '\')"><i class="mdi mdi-account-details"></i> Detail Peminjam</button>';
                $invoiceButton = '<button type="button" class="btn btn-sm btn-danger" onclick="showInvoiceFee(\'' . e($row->bulan) . '\')" title="Lihat invoice fee PDF"><i class="mdi mdi-file-pdf-box"></i> Invoice Fee</button>';

                return $detailButton . $invoiceButton;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function lenderLoanInvoiceFeeBreakdown(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'lender_id' => 'nullable|integer|exists:lenders,id',
            'fields' => 'sometimes|array',
            'fields.*' => 'string',
        ]);

        $fieldLabels = [
            'rate' => 'Biaya Bunga',
            'asuransi' => 'Biaya Asuransi',
            'provisi' => 'Biaya Provisi',
            'biaya_admin' => 'Biaya Admin',
            'fee_tomi' => 'Biaya TOMI',
            'biaya_layanan' => 'Biaya Layanan',
            'referral_1' => 'Biaya Referal 1',
            'referral_2' => 'Biaya Referal 2',
            'ksp' => 'Biaya Lender',
        ];
        $selectedFields = $request->has('fields')
            ? array_values(array_intersect($request->input('fields', []), array_keys($fieldLabels)))
            : array_keys($fieldLabels);

        $month = Carbon::createFromFormat('Y-m', $request->input('month'));
        $loans = pinjaman::query()
            ->where('status_otp', 'success')
            ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->when($request->filled('lender_id'), fn ($query) => $query->where('id_lender', $request->integer('lender_id')))
            ->get();

        $totalNominal = (float) $loans->sum('nominal');
        $weightedPercent = function (string $field) use ($loans, $totalNominal): float {
            if ($totalNominal <= 0) {
                return 0;
            }

            return round($loans->sum(fn ($loan) => (float) $loan->nominal * (float) ($loan->{$field} ?? 0)) / $totalNominal, 2);
        };
        $derivedPercent = function (float $amount) use ($totalNominal): float {
            return $totalNominal > 0 ? round($amount / $totalNominal * 100, 2) : 0;
        };

        $biayaAdmin = (float) $loans->sum('total_biaya_admin');
        $feeTomi = (float) $loans->sum('biaya_admin_pandu');
        $biayaLayanan = (float) $loans->sum('biaya_layanan');
        $ksp = (float) $loans->sum('ksp_fee');

        return response()->json([
            'data' => [
                'rate' => ['percent' => $weightedPercent('rate'), 'amount' => (float) $loans->sum('pendapatan_bunga')],
                'asuransi' => ['percent' => $weightedPercent('asuransi'), 'amount' => (float) $loans->sum('jumlah_asuransi')],
                'provisi' => ['percent' => $weightedPercent('provisi'), 'amount' => (float) $loans->sum('jumlah_provisi')],
                'biaya_admin' => ['percent' => $weightedPercent('biaya_admin'), 'amount' => $biayaAdmin],
                'fee_tomi' => ['percent' => $derivedPercent($feeTomi), 'amount' => $feeTomi],
                'biaya_layanan' => ['percent' => $derivedPercent($biayaLayanan), 'amount' => $biayaLayanan],
                'referral_1' => ['percent' => $weightedPercent('profesional_fee_1'), 'amount' => (float) $loans->sum('jumlah_profesional_1')],
                'referral_2' => ['percent' => $weightedPercent('profesional_fee_2'), 'amount' => (float) $loans->sum('jumlah_profesional_2')],
                'ksp' => ['percent' => $derivedPercent($ksp), 'amount' => $ksp],
                'total_nominal' => $totalNominal,
            ],
        ]);
    }

    public function downloadLenderLoanInvoiceFee(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'lender_id' => 'nullable|integer|exists:lenders,id',
            'fields' => 'sometimes|array',
            'fields.*' => 'string',
        ]);

        $fieldLabels = [
            'rate' => 'Biaya Bunga',
            'asuransi' => 'Biaya Asuransi',
            'provisi' => 'Biaya Provisi',
            'biaya_admin' => 'Biaya Admin',
            'fee_tomi' => 'Biaya TOMI',
            'biaya_layanan' => 'Biaya Layanan',
            'referral_1' => 'Biaya Referal 1',
            'referral_2' => 'Biaya Referal 2',
            'ksp' => 'Biaya Lender',
        ];
        $selectedFields = $request->has('fields')
            ? array_values(array_intersect($request->input('fields', []), array_keys($fieldLabels)))
            : array_keys($fieldLabels);

        $month = Carbon::createFromFormat('Y-m', $request->input('month'));
        $loans = pinjaman::with(['data_karyawan', 'perusahaan', 'lender'])
            ->where('status_otp', 'success')
            ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->when($request->filled('lender_id'), fn ($query) => $query->where('id_lender', $request->integer('lender_id')))
            ->orderBy('created_at')
            ->get();

        $items = $loans->values()->map(function ($loan, $index) {
            return [
                'no' => $index + 1,
                'tanggal_pengajuan' => optional($loan->created_at)->format('d/m/Y'),
                'nama' => $loan->data_karyawan?->nama_karyawan ?? '-',
                'perusahaan' => $loan->perusahaan?->nama ?? '-',
                'nip' => $loan->data_karyawan?->nip ?? '-',
                'jumlah_pinjaman' => (float) ($loan->nominal ?? 0),
                'biaya_layanan' => (float) ($loan->biaya_layanan ?? 0),
                'fee_components' => [
                    'rate' => (float) ($loan->pendapatan_bunga ?? 0),
                    'asuransi' => (float) ($loan->jumlah_asuransi ?? 0),
                    'provisi' => (float) ($loan->jumlah_provisi ?? 0),
                    'biaya_admin' => (float) ($loan->total_biaya_admin ?? 0),
                    'fee_tomi' => (float) ($loan->biaya_admin_pandu ?? 0),
                    'biaya_layanan' => (float) ($loan->biaya_layanan ?? 0),
                    'referral_1' => (float) ($loan->jumlah_profesional_1 ?? 0),
                    'referral_2' => (float) ($loan->jumlah_profesional_2 ?? 0),
                    'ksp' => (float) ($loan->ksp_fee ?? 0),
                ],
            ];
        });

        $selectedLender = $request->filled('lender_id')
            ? lender::find($request->integer('lender_id'))
            : $loans->first()?->lender;

        $invoice = (object) [
            'nomor' => 'TD/' . $month->format('Ym') . '/' . ($request->input('lender_id') ?: 'ALL'),
            'periode' => $month->locale('id')->translatedFormat('F Y'),
            'periode_mulai' => $month->copy()->startOfMonth()->format('Y-m-d'),
            'periode_selesai' => $month->copy()->endOfMonth()->format('Y-m-d'),
            'lender' => $selectedLender?->nama ?? 'Seluruh Lender',
            'lender_code' => $selectedLender?->id_code_lender ?? '-',
        ];

        $summary = [
            'total_pinjaman' => $items->sum('jumlah_pinjaman'),
            'total_biaya_layanan' => $items->sum('biaya_layanan'),
        ];
        $fieldTotals = collect($selectedFields)->mapWithKeys(fn ($field) => [
            $field => $items->sum(fn ($item) => $item['fee_components'][$field] ?? 0),
        ])->all();

        $viewData = compact('invoice', 'items', 'summary', 'fieldLabels', 'selectedFields', 'fieldTotals') + ['isPdf' => true];
        $pdf = Pdf::loadView('admin.tomi-dana.lender.invoice-fee', $viewData)
            ->setPaper('a4', 'landscape');

        if (! $request->boolean('download')) {
            return $pdf->stream('invoice-fee-tomidana-' . $month->format('Ym') . '.pdf');
        }

        return $pdf->download('invoice-fee-tomidana-' . $month->format('Ym') . '.pdf');
    }

    public function lenderLoansDetailTable(Request $request)
    {
        $query = pinjaman::with(['data_karyawan', 'perusahaan', 'lender']);

        $this->filterLenderLoans($query, $request);

        return DataTables::eloquent($query->latest())
            ->addIndexColumn()
            ->addColumn('nama_karyawan', fn ($row) => strtoupper($row->data_karyawan?->nama_karyawan ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('nominal', fn ($row) => 'Rp. ' . number_format((float) $row->nominal, 0, ',', '.'))
            ->addColumn('tenor_label', fn ($row) => ($row->tenor ?? 0) . ' bulan')
            ->addColumn('status_label', fn ($row) => '<span class="badge badge-secondary">' . e(strtoupper((string) ($row->status ?? '-'))) . '</span>')
            ->addColumn('tanggal_pengajuan', fn ($row) => optional($row->created_at)->format('d/m/Y H:i') ?? '-')
            ->rawColumns(['status_label'])
            ->toJson();
    }

    private function filterLenderLoans($query, Request $request)
    {
        $query->where('status_otp', 'success')
            ->when($request->filled('lender_id'), fn ($query) => $query->where('id_lender', $request->integer('lender_id')))
            ->when($request->filled('start_month') || $request->filled('end_month'), function ($query) use ($request) {
                $startDate = $request->filled('start_month')
                    ? Carbon::createFromFormat('Y-m', $request->input('start_month'))->startOfMonth()
                    : null;
                $endDate = $request->filled('end_month')
                    ? Carbon::createFromFormat('Y-m', $request->input('end_month'))->endOfMonth()
                    : null;

                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('created_at', '<=', $endDate);
                }
            });

        return $query;
    }

    public function storeLender(Request $request)
    {
        $data = $request->validate([
            'pic_lender' => 'required|string|max:255',
            'pic_internal' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email_lender' => 'required|email|max:255',
            'telp' => 'required|numeric',
            'alamat_lengkap' => 'required|string',
            'id_bank' => 'nullable|exists:banks,id',
            'nama_rekening' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:100',
            'nomor_pks' => 'nullable|string|max:255',
            'status' => 'required|in:active,nonactive',
        ]);

        $lender = new lender($data);
        $lender->id_code_lender = $this->generateLenderCode();
        $lender->created_by = auth('admin')->id();
        $lender->save();

        return response()->json(['status' => 200, 'message' => 'Lender berhasil ditambahkan.']);
    }

    public function detailLender(Request $request)
    {
        $lender = lender::with('bank')->findOrFail($request->id);

        return response()->json(['status' => 200, 'data' => $lender]);
    }

    public function updateLender(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:lenders,id',
            'pic_lender' => 'required|string|max:255',
            'pic_internal' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email_lender' => 'required|email|max:255',
            'telp' => 'required|numeric',
            'alamat_lengkap' => 'required|string',
            'id_bank' => 'nullable|exists:banks,id',
            'nama_rekening' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:100',
            'nomor_pks' => 'nullable|string|max:255',
            'status' => 'required|in:active,nonactive',
        ]);

        $lender = lender::findOrFail($data['id']);
        unset($data['id']);
        $lender->fill($data);
        $lender->updated_by = auth('admin')->id();
        $lender->save();

        return response()->json(['status' => 200, 'message' => 'Lender berhasil diubah.']);
    }

    public function deleteLender(Request $request)
    {
        $request->validate(['id' => 'required|exists:lenders,id']);
        lender::findOrFail($request->id)->delete();

        return response()->json(['status' => 200, 'message' => 'Lender berhasil dihapus.']);
    }

    private function generateLenderCode(): string
    {
        do {
            $code = '#TomiDanaLender-' . Carbon::now()->format('dmY') . '-' . random_int(1000, 9999);
        } while (lender::where('id_code_lender', $code)->exists());

        return $code;
    }

    public function companies()
    {
        $provinsis = provinsi::get();
        $perusahaan = ListPerusahaan::select('id', 'status')->get();

        return view('admin.tomi-dana.companies.index', [
            'provinsis' => $provinsis,
            'perusahaanTidakAktif' => $perusahaan->where('status', 'nonactive')->count(),
            'perusahaanAktif' => $perusahaan->where('status', 'active')->count(),
        ]);
    }

    public function kabupaten(Request $request)
    {
        return kota::where('provinsi', $request->provinsi)->get();
    }

    public function kecamatan(Request $request)
    {
        return kecamatan::where('kabupaten', $request->kabupaten)->get();
    }

    public function desa(Request $request)
    {
        return kelurahan::where('kecamatan', $request->kecamatan)->get();
    }

    public function addCompany(AddPerusahaanRequest $request)
    {
        $perusahaan = new ListPerusahaan();
        $perusahaan->id_code_perusahaan = $this->generateCompanyCode();
        $perusahaan->unique_code = $this->generateUniqueCode();
        $this->fillCompany($perusahaan, $request);
        $perusahaan->save();

        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Ditambahkan',
        ]);
    }

    public function detailCompany(Request $request)
    {
        $perusahaan = ListPerusahaan::with(['provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->findOrFail($request->id);

        return response()->json([
            'status' => 200,
            'data' => new DetailPerusahaanResource($perusahaan),
        ]);
    }

    public function updateCompany(EditPerusahaanRequest $request)
    {
        $perusahaan = ListPerusahaan::findOrFail($request->id);
        $this->fillCompany($perusahaan, $request);
        $perusahaan->status = $request->status;
        $perusahaan->save();

        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Diubah',
        ]);
    }

    public function deleteCompany(Request $request)
    {
        $request->validate(['id' => 'required|exists:list_perusahaans,id']);

        pinjaman::where('id_perusahaan', $request->id)->delete();
        list_karyawan::where('id_list_perusahaan', $request->id)->delete();
        ListPerusahaan::where('id', $request->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus Perusahaan, data karyawan, dan pinjaman',
        ]);
    }

    public function companiesTable()
    {
        try {
            $query = ListPerusahaan::query()
                ->select('list_perusahaans.*')
                ->with(['lender', 'karyawan', 'karyawanPending', 'pinjaman', 'provinsi', 'kabupaten', 'kecamatan', 'desa']);

            $admin = auth()->guard('admin')->user();
            if ($admin?->type === 'pic') {
                $query->when($admin->company_id, fn ($companyQuery) => $companyQuery->where('list_perusahaans.id', $admin->company_id));
                if (empty($admin->company_id)) {
                    $query->whereRaw('1 = 0');
                }
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('lender', fn ($row) => optional($row->lender)->nama ?? '-')
                ->addColumn('list_karyawan', function ($row) {
                    return '<a href="' . route('admin.list.karyawan', encryptSecure($row->id)) . '" target="_blank"><span class="badge badge-primary"><span class="badge badge-info">' . $row->karyawan->count() . '</span><span class="badge badge-pink">' . $row->karyawanPending->count() . '</span></span></a>';
                })
                ->addColumn('proses_pengajuan', function ($row) {
                    return '<a href="' . route('admin.ksp.pengajuan', encryptSecure($row->id)) . '" target="_blank"><span class="badge badge-primary"><span class="badge badge-warning">' . $row->pinjaman->where('status', 'on_proses')->count() . '</span><span class="badge badge-purple">' . $row->pinjaman->where('status', 'exported')->count() . '</span><span class="badge badge-info">' . $row->pinjaman->where('status', 'approve')->count() . '</span><span class="badge badge-pink">' . $row->pinjaman->where('status', 'reject')->count() . '</span></span></a>';
                })
                ->addColumn('provinsi', fn ($row) => optional($row->provinsi)->provinsi ?? '-')
                ->addColumn('unique_code', fn ($row) => $row->unique_code ?? '-')
                ->addColumn('kabupaten', fn ($row) => optional($row->kabupaten)->kabupaten ?? '-')
                ->addColumn('kecamatan', fn ($row) => optional($row->kecamatan)->kecamatan ?? '-')
                ->addColumn('desa', fn ($row) => $row->desa ? ($row->desa->desa ?? '-') . ' - ' . ($row->desa->kodepos ?? '-') : '-')
                ->addColumn('alamat_lengkap', fn ($row) => $row->alamat_lengkap ?? '-')
                ->addColumn('konfigurasi_pinjaman', fn ($row) => '<a href="' . route('admin.tomi-dana.configurations.index', encryptSecure($row->id)) . '" target="_blank"><i class="mdi mdi-settings"></i></a>')
                ->addColumn('status', function ($row) {
                    return match ($row->status) {
                        'active' => '<span class="badge badge-success">ACTIVE</span>',
                        'nonactive' => '<span class="badge badge-warning">INACTIVE</span>',
                        default => '<span class="badge badge-secondary">UNKNOWN</span>',
                    };
                })
                ->addColumn('action', fn ($row) => '<button type="button" class="btn btn-sm btn-primary mr-1" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deletePerusahaan(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
                ->filterColumn('lender', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
                ->filterColumn('provinsi', fn ($query, $keyword) => $query->whereHas('provinsi', fn ($q) => $q->where('provinsi', 'like', "%{$keyword}%")))
                ->filterColumn('kabupaten', fn ($query, $keyword) => $query->whereHas('kabupaten', fn ($q) => $q->where('kabupaten', 'like', "%{$keyword}%")))
                ->filterColumn('kecamatan', fn ($query, $keyword) => $query->whereHas('kecamatan', fn ($q) => $q->where('kecamatan', 'like', "%{$keyword}%")))
                ->filterColumn('desa', fn ($query, $keyword) => $query->whereHas('desa', fn ($q) => $q->where('desa', 'like', "%{$keyword}%")->orWhere('kodepos', 'like', "%{$keyword}%")))
                ->rawColumns(['status', 'action', 'proses_pengajuan', 'list_karyawan', 'konfigurasi_pinjaman'])
                ->make(true);
        } catch (\Throwable $exception) {
            Log::error('ERROR TOMI DANA COMPANIES TABLE', ['message' => $exception->getMessage()]);

            return response()->json(['error' => true, 'message' => 'Gagal mengambil data perusahaan.'], 500);
        }
    }

    private function fillCompany(ListPerusahaan $perusahaan, Request $request): void
    {
        $perusahaan->pic_perusahaan = $request->pic_perusahaan;
        $perusahaan->pic_internal = $request->pic_internal;
        $perusahaan->nama = $request->nama_perusahaan;
        $perusahaan->email_perusahaan = $request->email_perusahaan ?? $perusahaan->email_perusahaan;
        $perusahaan->alamat_lengkap = $request->alamat_lengkap;
        $perusahaan->telp = $request->telepon;
        $perusahaan->payment_date = $request->payment_date;
        $perusahaan->nomor_pks = $request->nomor_pks;
        $perusahaan->id_provinsi = provinsi::where('provinsi', $request->provinsi)->value('id');
        $perusahaan->id_kabupaten = kota::where('kabupaten', $request->kabupaten)->value('id');
        $perusahaan->id_kecamatan = kecamatan::where('kecamatan', $request->kecamatan)->value('id');

        [$desa, $kodepos] = array_pad(explode('-', $request->desa, 2), 2, null);
        $perusahaan->id_desa = kelurahan::where('desa', $desa)->where('kodepos', $kodepos)->value('id');
    }

    private function generateCompanyCode(): string
    {
        do {
            $code = '#TomiDana-' . Carbon::now()->format('dmY') . '-' . random_int(1000, 9999);
        } while (ListPerusahaan::where('id_code_perusahaan', $code)->exists());

        return $code;
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
        } while (ListPerusahaan::where('unique_code', $code)->exists());

        return $code;
    }
}
