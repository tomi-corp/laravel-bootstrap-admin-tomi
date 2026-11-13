<?php

namespace App\Http\Controllers\Admin\TomiDana;

use App\Http\Controllers\Controller;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\ListPerusahaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class RepaymentController extends Controller
{
    public function repayments(Request $request, ?int $companyId = null)
    {
        if ($companyId === null) {
            $companies = ListPerusahaan::query()
                ->orderBy('nama')
                ->get(['id', 'id_code_perusahaan', 'nama', 'status']);

            $invoices = invoicing_pinjaman::with([
                'pinjaman' => function ($query) {
                    $query->whereIn('status', ['approve', 'done'])->with(['angsuran', 'data_karyawan', 'user']);
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
                        'invoice_id' => null,
                        'status_label' => '-',
                        'status_invoice' => '-',
                    ]];
                }

                return $companyInvoices->map(function ($invoice) use ($company) {
                    $invoicePeriod = $invoice->period
                        ? Carbon::parse($invoice->period)
                        : Carbon::createFromDate($invoice->tahun, $invoice->bulan, 1);
                    $targetPeriod = $invoicePeriod->format('m-Y');
                    $loans = $invoice->pinjaman->filter(function ($loan) use ($targetPeriod) {
                        return $loan->angsuran->contains(fn ($installment) => $installment->jatuh_tempo
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
                        'invoice' => 'INV-'.strtoupper($invoicePeriod->format('M')).'-'.$company->id_code_perusahaan.'-'.$invoice->id,
                        'invoice_id' => $invoice->id,
                        'status_label' => $this->repaymentTimelineStatus(
                            $invoicePeriod,
                            strtolower((string) $invoice->status) === 'lunas'
                        ),
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
                'loan_number' => 'TD-'.str_pad((string) ($installment->id_pinjaman ?? '-'), 6, '0', STR_PAD_LEFT),
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

    public function invoiceLender(int $invoiceId)
    {
        return view('admin.tomi-dana.repayments.invoice-lender', array_merge(
            $this->invoiceLenderData($invoiceId),
            ['isPdf' => false]
        ));
    }

    public function invoiceLenderPdf(int $invoiceId)
    {
        $data = array_merge($this->invoiceLenderData($invoiceId), ['isPdf' => true]);
        $pdf = Pdf::loadView('admin.tomi-dana.repayments.invoice-lender', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream($data['invoiceNumber'].'.pdf');
    }

    private function invoiceLenderData(int $invoiceId): array
    {
        $invoice = invoicing_pinjaman::with([
            'perusahaan.lender',
            'pinjaman' => function ($query) {
                $query->whereIn('status', ['approve', 'done'])->with(['angsuran', 'data_karyawan', 'user']);
            },
        ])->findOrFail($invoiceId);

        $company = $invoice->perusahaan;
        $invoicePeriod = $invoice->period
            ? Carbon::parse($invoice->period)
            : Carbon::createFromDate($invoice->tahun, $invoice->bulan, 1);
        $targetPeriod = $invoicePeriod->format('m-Y');
        $pinjaman = $invoice->pinjaman->filter(function ($loan) use ($targetPeriod) {
            return $loan->angsuran->contains(fn ($installment) => $installment->jatuh_tempo
                && Carbon::parse($installment->jatuh_tempo)->format('m-Y') === $targetPeriod
            );
        })->values();

        return [
            'invoice' => $invoice,
            'company' => $company,
            'invoicePeriod' => $invoicePeriod,
            'invoiceNumber' => 'INV-'.strtoupper($invoicePeriod->format('M')).'-'.$company->id_code_perusahaan.'-'.$invoice->id,
            'pinjaman' => $pinjaman,
            'jumlahPinjaman' => $pinjaman->count(),
            'totalTagihan' => $pinjaman->sum('total_angsuran_perbulan'),
        ];
    }

    public function updateRepaymentInvoice(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|integer|exists:invoicing_pinjaman,id',
            'payment_time' => 'required|date',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'declaration' => 'accepted',
        ]);

        $invoice = invoicing_pinjaman::findOrFail($data['invoice_id']);
        $proofPath = $request->file('payment_proof')->store('assets/invoices/proof');
        $invoice->status = 'lunas';

        if (Schema::hasColumn($invoice->getTable(), 'tanggal_pembayaran')) {
            $invoice->tanggal_pembayaran = $data['payment_time'];
        }

        foreach (['bukti_bayar', 'bukti_pembayaran', 'payment_proof'] as $proofColumn) {
            if (Schema::hasColumn($invoice->getTable(), $proofColumn)) {
                $invoice->{$proofColumn} = $proofPath;
                break;
            }
        }

        $invoice->save();

        return response()->json([
            'status' => 200,
            'message' => 'Pembayaran invoice berhasil disimpan.',
        ]);
    }

    private function repaymentTimelineStatus(?Carbon $dueDate, bool $isPaid): string
    {
        if ($isPaid) {
            return '<span class="badge badge-success">LUNAS</span>';
        }

        if (! $dueDate) {
            return '-';
        }

        $daysUntilDue = Carbon::today()->diffInDays($dueDate, false);
        $timeline = match (true) {
            $daysUntilDue >= 5 => ['H-5', '&#9898;', 'Belum Jatuh Tempo'],
            $daysUntilDue === 4 => ['H-4', '&#9898;', 'Belum Jatuh Tempo'],
            $daysUntilDue === 3 => ['H-3', '&#9898;', 'Belum Jatuh Tempo'],
            $daysUntilDue === 2 => ['H-2', '&#9898;', 'Belum Jatuh Tempo'],
            $daysUntilDue === 1 => ['H-1', '&#9898;', 'Belum Jatuh Tempo'],
            $daysUntilDue === 0 => ['H0', '&#128309;', 'Baru Masuk Tagihan'],
            $daysUntilDue === -1 => ['H+1', '&#128993;', 'Normal'],
            $daysUntilDue === -2 => ['H+2', '&#128992;', 'Mendesak'],
            default => ['H+3', '&#128308;', 'Sangat Mendesak'],
        };

        [$dayLabel, $symbol, $status] = $timeline;

        return $symbol.' '.$dayLabel.' - '.$status;
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
            ->addColumn('nominal', fn ($row) => 'Rp. '.number_format((float) ($row->pinjaman?->total_angsuran_perbulan ?? 0), 0, ',', '.'))
            ->addColumn('status_label', function ($row) {
                return $this->repaymentTimelineStatus(
                    $row->jatuh_tempo ? Carbon::parse($row->jatuh_tempo) : null,
                    strtolower((string) $row->status) === 'lunas'
                );
            })
            ->addColumn('action', fn () => '<button type="button" class="btn btn-sm btn-light" title="Detail" disabled><i class="mdi mdi-eye-outline"></i></button>')
            ->filterColumn('nama_peminjam', function ($query, $keyword) {
                $query->whereHas('pinjaman.data_karyawan', function ($borrowerQuery) use ($keyword) {
                    $borrowerQuery->where('nama_karyawan', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('nomor_pinjaman', function ($query, $keyword) {
                $query->whereHas('pinjaman', function ($loanQuery) use ($keyword) {
                    $loanQuery->where('id_pinjaman', 'like', '%'.ltrim($keyword, '#').'%');
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
}
