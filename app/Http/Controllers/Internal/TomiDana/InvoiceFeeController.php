<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lender\UpdateInvoiceRequest;
use App\Models\disbursement;
use App\Models\history_invoicing_lender;
use App\Models\history_invoicing_lender_update;
use App\Models\lender;
use App\Models\pinjaman;
use App\Models\rekening_pandu;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Biaya aplikasi TomiDana: invoice lender (pending & done).
 * Logika disalin dari Admin\LenderController (halaman KSP > Lender > Invoicing).
 * Approval perubahan status tetap dilakukan di halaman KSP.
 */
class InvoiceFeeController extends Controller
{
    private const PDF_COLUMNS = [
        'tanggal_pinjaman',
        'nip',
        'nominal_pinjaman',
        'rate',
        'fee_tomi',
        'biaya_layanan',
        'biaya_admin',
        'total',
    ];

    public function index()
    {
        return view('internal.tomi-dana.invoice-fee.index');
    }

    public function pendingTable()
    {
        return DataTables::of($this->invoiceTableQuery('belum_lunas'))
            ->addIndexColumn()
            ->filterColumn('id_code_lender', fn ($query, $keyword) => $query->where('lenders.id_code_lender', 'like', "%{$keyword}%"))
            ->filterColumn('nama_lender', fn ($query, $keyword) => $query->where('lenders.nama', 'like', "%{$keyword}%"))
            ->addColumn('pendingApproval', function ($row) {
                return $row->has_pending_approval
                    ? '<span class="badge badge-warning">LOCKED</span>'
                    : '<span class="badge badge-success">AVAILABLE TO EDIT</span>';
            })
            ->addColumn('period', fn ($row) => Carbon::parse($row->period)->format('Y-m-d'))
            ->addColumn('no_invoice', fn ($row) => e(strtoupper((string) $row->no_invoice)))
            ->addColumn('id_code_lender', fn ($row) => e(strtoupper((string) $row->id_code_lender)))
            ->addColumn('nama_lender', fn ($row) => e(strtoupper((string) $row->nama_lender)))
            ->addColumn('total_pinjaman', fn ($row) => '<span class="badge badge-success">'.number_format((int) $row->total_pinjaman, 0, ',', '.').'</span>')
            ->addColumn('revenue', fn ($row) => $this->rupiah($row->revenue))
            ->addColumn('download', function ($row) {
                return '<a href="javascript:void(0)" class="btn-download-invoice" data-id="'.encryptSecure($row->id).'"><i class="mdi mdi-download"></i></a>';
            })
            ->addColumn('action', function ($row) {
                return $row->has_pending_approval
                    ? '<button class="btn btn-sm btn-primary mr-2 disabled"><i class="mdi mdi-pencil"></i></button>'
                    : '<button class="btn btn-sm btn-primary mr-2" onclick="editInvoiceFee('.$row->id.')"><i class="mdi mdi-pencil"></i></button>';
            })
            ->rawColumns(['pendingApproval', 'no_invoice', 'id_code_lender', 'nama_lender', 'total_pinjaman', 'download', 'action'])
            ->only([
                'DT_RowIndex',
                'pendingApproval',
                'period',
                'no_invoice',
                'id_code_lender',
                'nama_lender',
                'total_pinjaman',
                'revenue',
                'download',
                'action',
            ])
            ->toJson();
    }

    public function doneTable()
    {
        return DataTables::of($this->invoiceTableQuery('lunas'))
            ->addIndexColumn()
            ->filterColumn('id_code_lender', fn ($query, $keyword) => $query->where('lenders.id_code_lender', 'like', "%{$keyword}%"))
            ->filterColumn('nama_lender', fn ($query, $keyword) => $query->where('lenders.nama', 'like', "%{$keyword}%"))
            ->addColumn('period', fn ($row) => Carbon::parse($row->period)->format('Y-m-d'))
            ->addColumn('no_invoice', fn ($row) => e(strtoupper((string) $row->no_invoice)))
            ->addColumn('id_code_lender', fn ($row) => e(strtoupper((string) $row->id_code_lender)))
            ->addColumn('nama_lender', fn ($row) => e(strtoupper((string) $row->nama_lender)))
            ->addColumn('total_pinjaman', fn ($row) => '<span class="badge badge-success">'.number_format((int) $row->total_pinjaman, 0, ',', '.').'</span>')
            ->addColumn('revenue', fn ($row) => $this->rupiah($row->revenue))
            ->addColumn('download', function ($row) {
                return '<a href="javascript:void(0)" class="btn-download-invoice" data-id="'.encryptSecure($row->id).'"><i class="mdi mdi-download"></i></a>';
            })
            ->rawColumns(['no_invoice', 'id_code_lender', 'nama_lender', 'total_pinjaman', 'download'])
            ->only([
                'DT_RowIndex',
                'period',
                'no_invoice',
                'id_code_lender',
                'nama_lender',
                'total_pinjaman',
                'revenue',
                'download',
            ])
            ->toJson();
    }

    public function update(UpdateInvoiceRequest $req)
    {
        $invoice = history_invoicing_lender::findOrFail($req->id);
        $oldData = $invoice->getOriginal();

        $invoice->status = 'lunas';
        $invoice->update_by = auth('admin')->id();
        $invoice->tanggal_pembayaran = $req->tanggal;

        $changedData = $invoice->getDirty();

        $logData = [
            'old_data' => array_intersect_key($oldData, $changedData),
            'new_data' => $changedData,
        ];

        $invoiceUpdate = new history_invoicing_lender_update;
        $invoiceUpdate->invoicing_lender_id = $invoice->id;
        $invoiceUpdate->new_data = json_encode($logData);
        $invoiceUpdate->requester_id = auth('admin')->id();
        $invoiceUpdate->status_pertama = 'pending';
        $invoiceUpdate->status_kedua = 'pending';
        $invoiceUpdate->save();

        return response()->json([
            'status' => 200,
            'message' => 'Request perubahan transaksi invoicing Lender Berhasil, Harap menunggu approval',
        ], 200);
    }

    public function download(Request $request, $id)
    {
        $id = decryptSecure($id);

        $selectedColumns = $request->get('columns', []);
        if (! is_array($selectedColumns)) {
            $selectedColumns = [];
        }
        $selectedColumns = array_values(array_intersect($selectedColumns, self::PDF_COLUMNS));
        if (empty($selectedColumns)) {
            $selectedColumns = self::PDF_COLUMNS;
        }

        $historyInvoicingLenderPending = history_invoicing_lender::where('id', $id)
            ->with([
                'lender.pinjaman.disbursement',
                'lender.pinjaman.data_karyawan',
                'lender.pinjaman.perusahaan',
                'pendingApproval',
            ])
            ->first();

        abort_unless($historyInvoicingLenderPending, 404, 'Data invoice tidak ditemukan.');

        if ($historyInvoicingLenderPending->lender) {
            $period = Carbon::parse($historyInvoicingLenderPending->period)->format('Y-m');

            $filteredPinjaman = $historyInvoicingLenderPending->lender->pinjaman
                ->filter(function ($pinjaman) use ($period) {
                    return in_array(strtolower((string) $pinjaman->status), ['approve', 'done'], true)
                        && $pinjaman->disbursement
                        && Carbon::parse($pinjaman->disbursement->created_at)->format('Y-m') === $period;
                })
                ->values();

            // Rate 1,5% (= Fee TOMI), biaya layanan Rp 10.000 per pinjaman, biaya admin 1%.
            $ratePersen = 1.5;
            $biayaLayananPerPinjaman = 10000;

            $totalRate = $filteredPinjaman->sum(fn ($pinjaman) => ($pinjaman->nominal ?? 0) * ($ratePersen / 100));
            $totalBiayaLayanan = $filteredPinjaman->count() * $biayaLayananPerPinjaman;
            $totalBiayaAdmin = $filteredPinjaman->sum(fn ($pinjaman) => ($pinjaman->nominal ?? 0) * 0.01);
            $totalFeeTomi = $totalRate;

            $lender = $historyInvoicingLenderPending->lender;
            $lender->total_pinjaman = $filteredPinjaman->count();
            $lender->total_rate = $totalRate;
            $lender->total_fee_tomi = $totalFeeTomi;
            $lender->total_biaya_layanan = $totalBiayaLayanan;
            $lender->total_biaya_admin = $totalBiayaAdmin;
            $lender->total_keseluruhan = $totalFeeTomi + $totalBiayaLayanan + $totalBiayaAdmin;
            $lender->setRelation('pinjaman', $filteredPinjaman);
        }

        $rekening = rekening_pandu::with('bank')->first();

        $pdf = Pdf::loadView(
            'admin.ksp.lender.invoicing.invoicing',
            compact('historyInvoicingLenderPending', 'rekening', 'selectedColumns')
        )
            ->setPaper('legal', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->stream('invoice-lender-'.$historyInvoicingLenderPending->no_invoice.'.pdf');
    }

    private function invoiceTableQuery(string $status): Builder
    {
        $invoiceTable = (new history_invoicing_lender)->getTable();
        $lenderTable = (new lender)->getTable();
        $pinjamanTable = (new pinjaman)->getTable();
        $disbursementTable = (new disbursement)->getTable();

        $matchingPeriodSql = "DATE_FORMAT({$disbursementTable}.created_at, '%Y-%m') = DATE_FORMAT({$invoiceTable}.period, '%Y-%m')";

        return history_invoicing_lender::query()
            ->where("{$invoiceTable}.status", $status)
            ->leftJoin($lenderTable, "{$lenderTable}.id", '=', "{$invoiceTable}.id_lender")
            ->select([
                "{$invoiceTable}.*",
                "{$lenderTable}.id_code_lender as id_code_lender",
                "{$lenderTable}.nama as nama_lender",
            ])
            ->withExists(['pendingApproval as has_pending_approval'])
            ->selectSub(function ($query) use ($invoiceTable, $pinjamanTable, $disbursementTable, $matchingPeriodSql) {
                $query->from($pinjamanTable)
                    ->join($disbursementTable, "{$disbursementTable}.pinjaman_id", '=', "{$pinjamanTable}.id")
                    ->whereColumn("{$pinjamanTable}.id_lender", "{$invoiceTable}.id_lender")
                    ->whereIn("{$pinjamanTable}.status", ['approve', 'done'])
                    ->whereNull("{$pinjamanTable}.deleted_at")
                    ->whereRaw($matchingPeriodSql)
                    ->selectRaw("COUNT(DISTINCT {$pinjamanTable}.id)");
            }, 'total_pinjaman')
            ->selectSub(function ($query) use ($invoiceTable, $pinjamanTable, $disbursementTable, $matchingPeriodSql) {
                $query->from($pinjamanTable)
                    ->join($disbursementTable, "{$disbursementTable}.pinjaman_id", '=', "{$pinjamanTable}.id")
                    ->whereColumn("{$pinjamanTable}.id_lender", "{$invoiceTable}.id_lender")
                    ->whereIn("{$pinjamanTable}.status", ['approve', 'done'])
                    ->whereNull("{$pinjamanTable}.deleted_at")
                    ->whereRaw($matchingPeriodSql)
                    ->selectRaw(
                        "COALESCE(SUM(CASE WHEN DATE({$pinjamanTable}.created_at) < STR_TO_DATE(CONCAT(YEAR({$invoiceTable}.period), '-04-17'), '%Y-%m-%d') THEN (COALESCE({$pinjamanTable}.nominal, 0) * 0.03) + 20000 ELSE 10000 + (COALESCE({$pinjamanTable}.nominal, 0) * 0.01) END), 0)"
                    );
            }, 'revenue');
    }

    private function rupiah($value): string
    {
        return 'Rp. '.number_format((float) $value, 0, ',', '.');
    }
}
