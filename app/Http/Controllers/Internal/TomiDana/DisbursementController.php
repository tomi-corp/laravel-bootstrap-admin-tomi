<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Concerns\ReadsUserDocuments;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\disbursement;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\notification;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Pencairan dana pinjaman TomiDana (semua lender), dibatasi perusahaan yang boleh diakses admin.
 */
class DisbursementController extends Controller
{
    use ReadsUserDocuments, ScopesCompanies;

    public function index()
    {
        return view('internal.tomi-dana.disbursement.index');
    }

    public function table()
    {
        $query = $this->loans()
            ->with(['data_karyawan.bank', 'data_karyawan.perusahaan', 'lender'])
            ->where('status', 'pending')
            ->where('document_privy', 'y');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('datetime', fn ($row) => Carbon::parse($row->created_at)->format('Y-m-d H:i:s'))
            ->addColumn('prioritas', function ($row) {
                $diffInHours = $row->created_at->diffInHours(Carbon::now());

                return match (true) {
                    $diffInHours >= 24 => '🔴 Sangat Mendesak',
                    $diffInHours >= 4 => '🟠 Mendesak',
                    $diffInHours >= 1 => '🟡 Normal',
                    default => '🔵 Baru Masuk',
                };
            })
            ->addColumn('doc_pk', fn ($row) => '<button class="btn btn-sm btn-info mr-2" onclick="documentPK('.$row->id.')"><i class="dripicons-document"></i></button>')
            ->addColumn('ktp', fn ($row) => '<button class="btn btn-sm btn-info mr-2" onclick="ktp('.$row->id.')"><i class="dripicons-user-id"></i></button>')
            ->addColumn('selfie', fn ($row) => '<button class="btn btn-sm btn-info mr-2" onclick="selfie('.$row->id.')"><i class="dripicons-user"></i></button>')
            ->editColumn('id_pinjaman', fn ($row) => '<b>'.e($row->id_pinjaman).'</b>')
            ->addColumn('lender', fn ($row) => $row->lender?->nama ?? '-')
            ->addColumn('nama_perusahaan', fn ($row) => $row->data_karyawan?->perusahaan?->nama ?? '-')
            ->addColumn('nama_karyawan', fn ($row) => $row->data_karyawan?->nama_karyawan ?? '-')
            ->addColumn('total_pinjaman', fn ($row) => 'Rp  '.number_format($row->nominal))
            ->addColumn('jumlah_disbursement', fn ($row) => '<mark>Rp  '.number_format($row->jumlah_yang_diterima).'</mark>')
            ->addColumn('bank', fn ($row) => $row->data_karyawan?->bank?->code_bank ?? '-')
            ->addColumn('no_rek', fn ($row) => $row->data_karyawan?->nomor_bank ?? '-')
            ->addColumn('atas_nama', fn ($row) => $row->data_karyawan?->nama_rekening ?? '-')
            ->addColumn('action', fn ($row) => '<button class="btn btn-sm btn-primary mr-2" onclick="edit('.$row->id.')"><i class="mdi mdi-pencil"></i></button>')
            ->rawColumns(['ktp', 'selfie', 'doc_pk', 'action', 'id_pinjaman', 'jumlah_disbursement'])
            ->toJson();
    }

    public function detail(Request $req)
    {
        $pinjaman = $this->loans()->with('data_karyawan.bank')->find($req->id);

        if (! $pinjaman) {
            return response()->json(['status' => 404, 'message' => 'Data pinjaman tidak ditemukan'], 404);
        }

        if (! $pinjaman->data_karyawan || ! $pinjaman->data_karyawan->bank) {
            return response()->json(['status' => 422, 'message' => 'Data karyawan atau bank pada pinjaman ini belum lengkap'], 422);
        }

        return [
            'id_pinjaman' => $pinjaman->id_pinjaman,
            'bank' => $pinjaman->data_karyawan->bank->code_bank,
            'atas_nama' => $pinjaman->data_karyawan->nama_rekening,
            'rekening' => $pinjaman->data_karyawan->nomor_bank,
            'disbursement' => 'Rp '.number_format($pinjaman->jumlah_yang_diterima),
        ];
    }

    public function action(Request $req)
    {
        $req->validate([
            'id' => ['required', 'integer'],
            'transfer_date' => ['required', 'date'],
            'transfer_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'transfer_date.required' => 'Tanggal transfer wajib diisi.',
            'transfer_date.date' => 'Format tanggal transfer tidak valid.',
            'transfer_proof.required' => 'Bukti transfer wajib diupload.',
            'transfer_proof.file' => 'Bukti transfer tidak valid.',
            'transfer_proof.mimes' => 'Bukti transfer harus berformat JPG, JPEG, PNG, atau PDF.',
            'transfer_proof.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);

        $pinjaman = $this->loans()->with(['data_karyawan.bank', 'perusahaan'])->find($req->id);

        if (! $pinjaman) {
            return response()->json(['status' => 404, 'message' => 'Data pinjaman tidak ditemukan'], 404);
        }

        if ($pinjaman->status !== 'pending') {
            return response()->json(['status' => 422, 'message' => 'Pinjaman ini sudah tidak berstatus pending'], 422);
        }

        if (! $pinjaman->perusahaan || empty($pinjaman->perusahaan->id_code_perusahaan)) {
            return response()->json(['status' => 422, 'message' => 'Data perusahaan pada pinjaman ini belum lengkap'], 422);
        }

        if (! $pinjaman->data_karyawan || ! $pinjaman->data_karyawan->bank) {
            return response()->json(['status' => 422, 'message' => 'Data karyawan atau bank pada pinjaman ini belum lengkap'], 422);
        }

        $file = $req->file('transfer_proof');
        $filename = now()->format('YmdHis').'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            .'.'.$file->getClientOriginalExtension();
        $transferProofPath = $file->storeAs('disbursements/bukti-transfer', $filename, 'public');

        try {
            DB::transaction(function () use ($pinjaman, $req, $transferProofPath) {
                foreach (jatuh_tempo::where('id_pinjaman', $pinjaman->id)->get() as $jatuhTempo) {
                    $period = Carbon::parse($jatuhTempo->jatuh_tempo);
                    $invoicing = invoicing_pinjaman::whereYear('period', $period->format('Y'))
                        ->whereMonth('period', $period->format('m'))
                        ->where('id_list_perusahaan', $pinjaman->id_perusahaan)
                        ->first();

                    if (! $invoicing) {
                        $invoicing = new invoicing_pinjaman;
                        $invoicing->id_list_perusahaan = $pinjaman->id_perusahaan;
                        $invoicing->period = $period->format('Y-m-d');
                        $invoicing->status = 'belum_lunas';
                        $invoicing->save();
                        $invoicing->no_invoice = "INV-{$period->format('M')}-{$pinjaman->perusahaan->id_code_perusahaan}-{$invoicing->id}";
                    }

                    $invoicing->status = 'belum_lunas';
                    $invoicing->save();
                }

                $disbursement = new disbursement;
                $disbursement->pinjaman_id = $pinjaman->id;
                $disbursement->admin_id = auth('admin')->id();
                $disbursement->bank_id = $pinjaman->data_karyawan->bank->id;
                $disbursement->dana_yang_dicairkan = $pinjaman->jumlah_yang_diterima;
                $disbursement->atas_nama = $pinjaman->data_karyawan->nama_rekening;
                $disbursement->nomor_rekening = $pinjaman->data_karyawan->nomor_bank;
                $disbursement->transfer_date = $req->transfer_date;
                $disbursement->transfer_proof = $transferProofPath;
                $disbursement->save();

                $notif = new notification;
                $notif->user_id = $pinjaman->user_id;
                $notif->title = 'Pengajuan Anda Telah Diperbarui';
                $notif->description = 'Selamat! Status pengajuan Anda telah berhasil dikirimkan ke rekening anda!';
                $notif->type = 'pinjaman_payroll';
                $notif->read = 'belum';
                $notif->save();

                $pinjaman->status = 'approve';
                $pinjaman->save();
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($transferProofPath);
            throw $e;
        }

        return response()->json(['status' => 200, 'message' => 'Berhasil mengubah status pinjaman'], 200);
    }

    public function utils()
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();
        $disbursementQuery = disbursement::whereHas('pinjaman', fn ($q) => $this->scopeToVisibleCompanies($q));

        $data = (clone $disbursementQuery)
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('DATE(created_at) as date, SUM(dana_yang_dicairkan) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $labels = [];
        $series = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $labels[] = $date->format('d M');
            $series[] = (float) ($data[$date->toDateString()] ?? 0);
        }

        return [
            'total_disburement' => 'Rp  '.number_format((clone $disbursementQuery)->sum('dana_yang_dicairkan')),
            'disbursmentHariIni' => (clone $disbursementQuery)->whereDate('created_at', Carbon::today())->count(),
            'totalDisbursementPending' => $this->loans()->where('status', 'pending')->count(),
            'labels' => $labels,
            'series' => [$series],
        ];
    }

    public function pk(Request $req)
    {
        $pinjaman = $this->loans()->findOrFail($req->id);

        return $this->documentResponse($this->pkDocumentPath($pinjaman->document_name), 'Dokumen PK tidak ditemukan.', 'application/pdf');
    }

    public function ktp(Request $req)
    {
        $pinjaman = $this->loans()->with('kyc')->findOrFail($req->id);

        return $this->documentResponse($this->loanImagePath($pinjaman, 'ktp'), 'Gambar KTP tidak ditemukan.', 'image/jpeg');
    }

    public function selfie(Request $req)
    {
        $pinjaman = $this->loans()->with('kyc')->findOrFail($req->id);

        return $this->documentResponse($this->loanImagePath($pinjaman, 'selfie'), 'Gambar Selfie tidak ditemukan.', 'image/jpeg');
    }

    /** Pinjaman milik perusahaan yang boleh diakses admin yang login. */
    private function loans(): Builder
    {
        return $this->scopeToVisibleCompanies(pinjaman::query());
    }

    private function scopeToVisibleCompanies(Builder $query): Builder
    {
        return $query->whereIn('id_perusahaan', $this->visibleCompanies()->select('list_perusahaans.id'));
    }

    private function documentResponse(?string $path, string $notFoundMessage, string $fallbackMime)
    {
        $disk = Storage::disk('local');
        abort_unless($path && $disk->exists($path), 404, $notFoundMessage);

        return response($disk->get($path), 200, [
            'Content-Type' => $disk->mimeType($path) ?: $fallbackMime,
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
