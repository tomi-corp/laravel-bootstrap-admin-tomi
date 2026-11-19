<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Http\Resources\Admin\Ksp\CheckAngsuranResource;
use App\Models\history_invoicing_lender;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\lender;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Proses pengajuan pinjaman TomiDana per perusahaan: statistik, tabel per status, tagihan, dan detail angsuran.
 */
class LoanApplicationController extends Controller
{
    use ScopesCompanies;

    /** Status pengajuan yang punya tabel sendiri di halaman. */
    public const STATUSES = ['on_proses', 'pending', 'approve', 'reject', 'done'];

    public function index(int $company)
    {
        $perusahaan = $this->findVisibleCompany($company)->load(['provinsi', 'kabupaten', 'kecamatan', 'desa']);

        return view('internal.tomi-dana.applications.index', compact('perusahaan'));
    }

    public function statistic(Request $req)
    {
        $company = $this->findVisibleCompany($req->id);
        $base = fn () => pinjaman::where('id_perusahaan', $company->id);
        $validated = fn (string $status) => $base()->where('status_otp', 'success')->where('status', $status)->count();
        $onProcess = $validated('on_proses');

        return response()->json([
            'status' => 200,
            'belum_validasi_wa' => $base()->where('status_otp', 'pending')->count(),
            'on_proses' => $onProcess,
            'onproses' => $onProcess,
            'pending' => $validated('pending'),
            'approve' => $validated('approve'),
            'done' => $validated('done'),
        ], 200);
    }

    /**
     * Tabel pengajuan per status. Filter tanggal opsional dari daterangepicker ("m/d/Y - m/d/Y").
     */
    public function table(Request $req, string $status)
    {
        abort_unless(in_array($status, self::STATUSES, true), 404);
        $company = $this->findVisibleCompany($req->id);
        $withInstallments = in_array($status, ['approve', 'done'], true);

        $query = pinjaman::where('id_perusahaan', $company->id)
            ->where('status', $status)
            ->where('status_otp', 'success')
            ->with($withInstallments ? ['data_karyawan', 'angsuran'] : ['data_karyawan']);

        if ($range = $this->dateRange($req->range)) {
            $query->whereBetween('created_at', $range);
        }

        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');
        $table = DataTables::of($query->get())
            ->addIndexColumn()
            ->addColumn('nama', fn ($row) => Str::upper($row->data_karyawan?->nama_karyawan ?? '-'))
            ->addColumn('nominal', fn ($row) => $rupiah($row->nominal))
            ->addColumn('angsuran_perbulan', fn ($row) => $rupiah($row->total_angsuran_perbulan))
            ->addColumn('tenor', fn ($row) => $row->tenor.' bulan')
            ->addColumn('biaya_admin_ksp', fn ($row) => $rupiah($row->biaya_admin_ksp))
            ->addColumn('dpp_adm_ksp', fn ($row) => $rupiah($row->dpp_adm_ksp))
            ->addColumn('ppn_adm_ksp', fn ($row) => $rupiah($row->ppn_adm_ksp))
            ->addColumn('biaya_admin_pandu', fn ($row) => $rupiah($row->biaya_admin_pandu))
            ->addColumn('dpp_adm_pandu', fn ($row) => $rupiah($row->dpp_adm_pandu))
            ->addColumn('ppn_adm_pandu', fn ($row) => $rupiah($row->ppn_adm_pandu))
            ->addColumn('rate', fn ($row) => $row->rate.' %')
            ->addColumn('iuran_anggota', fn ($row) => $rupiah($row->iuran_anggota))
            ->addColumn('biaya_layanan', fn ($row) => $rupiah($row->biaya_layanan))
            ->addColumn('total_biaya_admin', fn ($row) => $rupiah($row->total_biaya_admin))
            ->addColumn('jumlah_yang_diterima', fn ($row) => $rupiah($row->jumlah_yang_diterima));

        if ($withInstallments) {
            $table->addColumn('biaya', fn ($row) => $rupiah($row->cost_tomi))
                ->addColumn('cost_tomi', fn ($row) => $rupiah($row->cost_tomi))
                ->addColumn('pendapatan_pandu', fn ($row) => $rupiah($row->dpp_adm_pandu - $row->cost_tomi))
                ->addColumn('detail_angsuran', fn ($row) => '<span class="text-center align-middle">'
                    .$row->angsuran->where('status', 'lunas')->count().'/'.$row->angsuran->count().'</span><br>'
                    .'<button class="btn btn-sm btn-primary mr-2" onclick="detailAngsuran('.$row->id.')"><i class="mdi mdi-eye"></i></button>')
                ->rawColumns(['detail_angsuran']);
        }

        return $table->toJson();
    }

    public function installments(Request $req)
    {
        $loan = pinjaman::with('angsuran')->findOrFail((int) $req->id);
        $this->authorizeCompanyId($loan->id_perusahaan);

        return response()->json([
            'status' => 200,
            'message' => CheckAngsuranResource::collection($loan->angsuran),
        ], 200);
    }

    public function invoiceTable(Request $req)
    {
        $company = $this->findVisibleCompany($req->id);
        $invoices = invoicing_pinjaman::where('id_list_perusahaan', $company->id)->get();
        $currentPeriod = now()->format('Ym');

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('period', fn ($row) => $row->tahun.' - '.$row->bulan)
            ->addColumn('total_tagihan', fn ($row) => 'Rp. '.number_format((float) ($row->biaya_admin_pandu + $row->biaya_layanan), 0, ',', '.'))
            ->addColumn('status_invoicing', fn ($row) => $row->period === null ? 'BELUM LUNAS' : 'LUNAS')
            ->addColumn('download_invoice', function ($row) use ($currentPeriod) {
                $available = $row->period === null && Carbon::createFromDate($row->tahun, $row->bulan, 1)->format('Ym') <= $currentPeriod;

                return $available
                    ? '<a href="'.route('internal.tomi-dana.applications.invoices.download', $row->id).'" target="_blank" rel="noopener"><i class="mdi mdi-download"></i></a>'
                    : '<span class="text-muted"><i class="mdi mdi-download"></i></span>';
            })
            ->addColumn('action', function ($row) use ($currentPeriod) {
                $editable = $row->period === null && Carbon::createFromDate($row->tahun, $row->bulan, 1)->format('Ym') <= $currentPeriod;

                return $editable
                    ? '<button class="btn btn-sm btn-primary mr-2" onclick="editInvoicing('.(int) $row->tahun.','.(int) $row->bulan.')"><i class="mdi mdi-pencil"></i></button>'
                    : '-';
            })
            ->rawColumns(['download_invoice', 'action'])
            ->only(['id', 'period', 'total_tagihan', 'status_invoicing', 'download_invoice', 'action'])
            ->toJson();
    }

    /**
     * Tandai tagihan periode (tahun/bulan) sudah dibayar.
     */
    public function invoiceUpdate(Request $req)
    {
        $req->validate([
            'id' => 'required|integer',
            'tahun' => 'required|integer|min:2000',
            'bulan' => 'required|integer|between:1,12',
        ]);
        $company = $this->findVisibleCompany($req->id);

        $history = new history_invoicing_lender;
        $history->periode = Carbon::createFromDate($req->tahun, $req->bulan, 1);
        $history->id_list_perusahaan = $company->id;
        $history->save();

        return response()->json([
            'status' => 200,
            'message' => 'Tagihan berhasil diperbarui.',
        ], 200);
    }

    public function invoiceDownload(int $invoice)
    {
        $invoice = invoicing_pinjaman::findOrFail($invoice);
        $company = $this->findVisibleCompany($invoice->id_list_perusahaan);
        $periodMonth = Carbon::parse($invoice->period)->format('m');

        $invoice->load(['pinjaman' => fn ($query) => $query->whereHas('jatuh_tempoSesuaiInvoice', fn ($q) => $q->whereMonth('jatuh_tempo', $periodMonth)), 'pinjaman.angsuran']);
        $pinjaman = $invoice->pinjaman;
        $noInv = 'INV-'.Carbon::parse($invoice->period)->format('M').'-'.$company->id_code_perusahaan.'-'.$invoice->id;
        $lender = lender::where('id', auth('admin')->user()->id_lender)->with('bank')->first();

        return view('admin.ksp.invoicing.perusahaan.index', compact('invoice', 'lender', 'pinjaman', 'noInv'));
    }

    private function dateRange(?string $range): ?array
    {
        $dates = explode(' - ', (string) $range);
        if (count($dates) !== 2) {
            return null;
        }

        try {
            return [
                Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay(),
                Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay(),
            ];
        } catch (\Throwable) {
            return null;
        }
    }
}
