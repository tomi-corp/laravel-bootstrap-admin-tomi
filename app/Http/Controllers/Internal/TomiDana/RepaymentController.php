<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\notification;
use App\Models\pinjaman;
use App\Models\repayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Pembayaran (repayment) invoice perusahaan TomiDana (semua lender), dibatasi perusahaan yang boleh diakses admin.
 */
class RepaymentController extends Controller
{
    use ScopesCompanies;

    public function index()
    {
        return view('internal.tomi-dana.repayment.index');
    }

    public function utils()
    {
        $nominal = $this->scopeToVisibleCompanies(pinjaman::query(), 'id_perusahaan')
            ->where('status', 'approve')
            ->sum('nominal');
        $totalRepayment = repayment::whereHas('invoice', fn ($q) => $this->scopeToVisibleCompanies($q, 'id_list_perusahaan'))
            ->sum('amount_total');

        return [
            'outstanding' => 'Rp  '.number_format($nominal - $totalRepayment),
            'totalRepayment' => 'Rp  '.number_format($totalRepayment),
        ];
    }

    public function table()
    {
        $invoiceTable = (new invoicing_pinjaman)->getTable();

        $query = $this->invoices()
            ->select([
                $invoiceTable.'.*',
                'list_perusahaans.id_code_perusahaan as company_id_code_perusahaan',
                'list_perusahaans.nama as company_nama',
                'list_perusahaans.h_min_jatuh_tempo as company_h_min_jatuh_tempo',
                'lenders.nama as lender_nama',
            ])
            ->leftJoin('list_perusahaans', 'list_perusahaans.id', '=', $invoiceTable.'.id_list_perusahaan')
            ->leftJoin('lenders', 'lenders.id', '=', 'list_perusahaans.id_lender');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('id_code_perusahaan', fn ($row) => '<b>'.e($row->company_id_code_perusahaan ?? '-').'</b>')
            ->addColumn('nama_perusahaan', fn ($row) => '<b>'.e($row->company_nama ?? '-').'</b>')
            ->addColumn('lender', fn ($row) => $row->lender_nama ?? '-')
            ->addColumn('prioritas', function ($row) {
                if ($row->status == 'lunas') {
                    return '<span class="badge badge-success">Lunas</span>';
                }

                $diffInDays = (int) Carbon::parse($row->period)->startOfDay()->diffInDays(Carbon::today(), false);

                return match (true) {
                    $diffInDays <= -5 => '⚪️ Belum Jatuh Tempo (H-5)',
                    $diffInDays < 0 => '⚪️ Belum Jatuh Tempo (H'.$diffInDays.')',
                    $diffInDays == 0 => '🔵 Baru Masuk (H0)',
                    $diffInDays == 1 => '🟡 Normal (H+1)',
                    $diffInDays == 2 => '🟠 Mendesak (H+2)',
                    default => '🔴 Sangat Mendesak (H+'.$diffInDays.')',
                };
            })
            ->addColumn('tanggal_period', fn ($row) => Carbon::parse($row->period)->format('Y-m-d'))
            // Jumlah pinjaman yang ditagih, contoh: 22 Pinjaman
            ->addColumn('total_tagihan', fn ($row) => '<button class="btn btn-sm btn-primary mr-2" onclick="showJatuhTempo('.$row->id.')"><i class="dripicons-checklist"></i></button>'
                .$this->tagihanPinjamanQuery($row)->count().' Pinjaman')
            // Nominal total angsuran, contoh: Rp 29,106,000
            ->addColumn('total_pinjaman', fn ($row) => 'Rp '.number_format($this->tagihanPinjamanQuery($row)->sum('total_angsuran_perbulan')))
            ->addColumn('download_invoice', function ($row) {
                $disabled = '<span class="text-muted"><i class="mdi mdi-download"></i></span>';
                $bisaDiunduh = Carbon::parse($row->period)->subDays($row->company_h_min_jatuh_tempo ?? 0)->format('Ymd') < Carbon::now()->format('Ymd');

                if ($row->status == 'lunas' || ! $bisaDiunduh) {
                    return $disabled;
                }

                return '<a href="'.route('internal.tomi-dana.applications.invoices.download', $row->id).'" target="_blank"><i class="mdi mdi-download"></i></a>';
            })
            ->editColumn('status', fn ($row) => $row->status == 'belum_lunas' ? 'Belum Lunas' : 'Lunas')
            ->addColumn('action', fn ($row) => $row->status == 'belum_lunas'
                ? '<button class="btn btn-sm btn-primary mr-2" onclick="edit('.$row->id.')"><i class="mdi mdi-pencil"></i></button>'
                : '-')
            ->rawColumns(['nama_perusahaan', 'download_invoice', 'id_code_perusahaan', 'prioritas', 'action', 'total_tagihan'])
            ->toJson();
    }

    public function list(Request $req)
    {
        $invoice = $this->findInvoice($req->id);

        return response()->json([
            'status' => 200,
            'data' => $this->validJatuhTempo($invoice)->map(fn ($jatuhTempo) => [
                'id_pinjaman' => $jatuhTempo->pinjaman?->id_pinjaman ?? '-',
                'nama' => $jatuhTempo->pinjaman?->data_karyawan?->nama_karyawan ?? '-',
                'angsuran_perbulan' => $jatuhTempo->pinjaman ? 'Rp  '.number_format($jatuhTempo->pinjaman->total_angsuran_perbulan) : '-',
            ])->values(),
        ], 200);
    }

    public function action(Request $req)
    {
        $req->validate([
            'id' => 'required|integer',
            'tanggal' => 'required|date_format:Y-m-d\TH:i',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'tanggal.required' => 'Waktu pembayaran wajib diisi.',
            'tanggal.date_format' => 'Format waktu pembayaran tidak valid.',
            'bukti_transfer.required' => 'Bukti transfer wajib diupload.',
            'bukti_transfer.file' => 'Bukti transfer tidak valid.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berformat JPG, JPEG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);
        $invoice = $this->findInvoice($req->id);

        if ($invoice->status === 'lunas') {
            return response()->json(['status' => 422, 'message' => 'Invoice ini sudah lunas'], 422);
        }

        $file = $req->file('bukti_transfer');
        $filename = now()->format('YmdHis').'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            .'.'.$file->getClientOriginalExtension();
        $proofPath = $file->storeAs('repayments/bukti-transfer', $filename, 'public');

        try {
            $this->settleInvoice($invoice, $req, $proofPath);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($proofPath);
            throw $e;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Invoicing berhasil diubah status nya dibayarkan!',
        ], 200);
    }

    /** Lunasi angsuran periode invoice, tandai invoice lunas, dan catat repayment beserta bukti transfernya. */
    private function settleInvoice(invoicing_pinjaman $invoice, Request $req, string $proofPath): void
    {
        DB::transaction(function () use ($invoice, $req, $proofPath) {
            $totalTagihan = $this->tagihanPinjamanQuery($invoice)->sum('total_angsuran_perbulan');

            foreach ($this->validJatuhTempo($invoice) as $jatuhTempo) {
                $pinjaman = $jatuhTempo->pinjaman;

                // Lunasi cicilan pertama yang belum lunas.
                $cicilan = jatuh_tempo::where('id_pinjaman', $pinjaman->id)
                    ->where('status', 'belum')
                    ->orderBy('pembayaran', 'asc')
                    ->first();

                if ($cicilan) {
                    $cicilan->status = 'lunas';
                    $cicilan->save();
                }

                $this->notify($pinjaman->user_id, 'Cicilanmu Berhasil Dibayarkan', 'Selamat! Pembayaran cicilanmu untuk periode ini sudah berhasil kami terima!');

                $jumlahLunas = jatuh_tempo::where('id_pinjaman', $pinjaman->id)->where('status', 'lunas')->count();
                if ($pinjaman->tenor == $jumlahLunas) {
                    $this->notify($pinjaman->user_id, 'Akhirnya Lunas!', 'Hebat! Pembayaran cicilan Anda telah lunas. Sekarang, Anda bebas dari kewajiban pembayaran. Kami tunggu transaksi selanjutnya!');

                    $pinjaman->status = 'done';
                    $pinjaman->save();
                }
            }

            $invoice->status = 'lunas';
            $invoice->save();

            $repayment = new repayment;
            $repayment->id_invoicing = $invoice->id;
            $repayment->admin_id = auth('admin')->id();
            $repayment->amount_total = $totalTagihan;
            $repayment->tanggal_pembayaran = $req->tanggal;
            $repayment->payment_proof_date = Carbon::parse($req->tanggal)->toDateString();
            $repayment->payment_proof_file = $proofPath;
            $repayment->save();
        });
    }

    /** Invoice milik perusahaan yang boleh diakses admin yang login. */
    private function invoices(): Builder
    {
        $invoiceTable = (new invoicing_pinjaman)->getTable();

        return $this->scopeToVisibleCompanies(invoicing_pinjaman::query(), $invoiceTable.'.id_list_perusahaan');
    }

    private function findInvoice($id): invoicing_pinjaman
    {
        return $this->invoices()
            ->with(['jatuh_tempo.pinjaman' => fn ($q) => $q->where('status', 'approve'), 'jatuh_tempo.pinjaman.data_karyawan'])
            ->findOrFail($id);
    }

    private function scopeToVisibleCompanies(Builder $query, string $column): Builder
    {
        return $query->whereIn($column, $this->visibleCompanies()->select('list_perusahaans.id'));
    }

    /** Pinjaman aktif perusahaan invoice yang punya angsuran belum lunas di bulan periode invoice. */
    private function tagihanPinjamanQuery(invoicing_pinjaman $invoice): Builder
    {
        $period = Carbon::parse($invoice->period);

        return pinjaman::where('id_perusahaan', $invoice->id_list_perusahaan)
            ->where('status', 'approve')
            ->whereHas('angsuran', fn ($q) => $q->whereYear('jatuh_tempo', $period->format('Y'))
                ->whereMonth('jatuh_tempo', $period->format('m'))
                ->where('status', '!=', 'lunas'));
    }

    /** Angsuran belum lunas di periode invoice yang pinjamannya aktif dan milik perusahaan invoice. */
    private function validJatuhTempo(invoicing_pinjaman $invoice)
    {
        $period = Carbon::parse($invoice->period)->format('m-Y');

        return $invoice->jatuh_tempo->filter(fn ($item) => $item->pinjaman
            && (int) $item->pinjaman->id_perusahaan === (int) $invoice->id_list_perusahaan
            && $item->pinjaman->status === 'approve'
            && $item->status !== 'lunas'
            && Carbon::parse($item->jatuh_tempo)->format('m-Y') === $period);
    }

    private function notify($userId, string $title, string $description): void
    {
        $notif = new notification;
        $notif->user_id = $userId;
        $notif->title = $title;
        $notif->description = $description;
        $notif->type = 'pinjaman_payroll';
        $notif->read = 'belum';
        $notif->save();
    }
}
