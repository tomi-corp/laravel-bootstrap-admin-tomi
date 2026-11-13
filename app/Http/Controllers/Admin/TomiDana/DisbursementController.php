<?php

namespace App\Http\Controllers\Admin\TomiDana;

use App\Http\Controllers\Controller;
use App\Models\disbursement;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DisbursementController extends Controller
{
    public function disbursements()
    {
        $statusCounts = pinjaman::query()
            ->whereRaw('LOWER(TRIM(status_otp)) = ?', ['success'])
            ->whereRaw('LOWER(TRIM(ttd)) = ?', ['y'])
            ->whereRaw('LOWER(TRIM(status)) IN (?, ?, ?)', ['pending', 'approve', 'done'])
            ->selectRaw('LOWER(TRIM(status)) as status, COUNT(*) as total')
            ->groupByRaw('LOWER(TRIM(status))')
            ->pluck('total', 'status');

        return view('admin.tomi-dana.disbursements.index', [
            'statusCounts' => [
                'belum_diproses' => (int) ($statusCounts['pending'] ?? 0),
                'aktif' => (int) ($statusCounts['approve'] ?? 0),
                'lunas' => (int) ($statusCounts['done'] ?? 0),
            ],
        ]);
    }

    public function disbursementsTable()
    {
        $statusMap = [
            'belum_diproses' => 'pending',
            'aktif' => 'approve',
            'lunas' => 'done',
        ];
        $loanStatus = $statusMap[request()->input('status_tab', 'belum_diproses')] ?? 'pending';

        $query = pinjaman::with(['user.kyc', 'user.privy', 'data_karyawan.bank', 'lender', 'perusahaan', 'disbursement'])
            ->whereRaw('LOWER(TRIM(status_otp)) = ?', ['success'])
            ->whereRaw('LOWER(TRIM(ttd)) = ?', ['y'])
            ->whereRaw('LOWER(TRIM(status)) = ?', [$loanStatus])
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
            ->addColumn('loan_id', fn ($row) => $row->id)
            ->addColumn('tanggal', fn ($row) => $row->created_at ? Carbon::parse($row->created_at)->format('d/m/Y') : '-')
            ->addColumn('prioritas', function ($row) {
                $ageHours = $row->created_at
                    ? max(0, Carbon::parse($row->created_at)->diffInHours(now(), false))
                    : 0;

                return match (true) {
                    $ageHours >= 24 => '&#128308; Sangat Mendesak',
                    $ageHours >= 4 => '&#128992; Mendesak',
                    $ageHours >= 1 => '&#128993; Normal',
                    default => '&#128309; Baru Masuk',
                };
            })
            ->addColumn('doc_pk', function ($row) {
                return $row->document_name
                    ? '<a href="'.route('admin.tomi-dana.disbursements.evidence', [$row->id, 'tte']).'" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat dokumen PK"><i class="mdi mdi-file-document-outline"></i></a>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('ktp', function ($row) {
                return '<a href="'.route('admin.tomi-dana.disbursements.evidence', [$row->id, 'ktp']).'" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat KTP"><i class="mdi mdi-account-box-outline"></i></a>';
            })
            ->addColumn('selfie', function ($row) {
                $path = $this->evidencePath('assets/users', $row->user?->privy?->local_selfie_path)
                    ?? $this->evidencePath('assets/users', $row->user?->kyc?->selfie);

                return $path
                    ? '<a href="'.route('admin.tomi-dana.disbursements.evidence', [$row->id, 'selfie']).'" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat selfie"><i class="mdi mdi-camera-outline"></i></a>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('id_pinjaman', fn ($row) => $row->id_pinjaman ?? ('TD-'.$row->id))
            ->addColumn('nama_perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('nama_karyawan', fn ($row) => strtoupper($row->data_karyawan?->nama_karyawan ?? $row->user?->name ?? '-'))
            ->addColumn('total_pinjaman', fn ($row) => 'Rp. '.number_format((float) ($row->nominal ?? 0), 0, ',', '.'))
            ->addColumn('jumlah_disbursement', fn ($row) => 'Rp. '.number_format((float) ($row->jumlah_yang_diterima ?? 0), 0, ',', '.'))
            ->addColumn('bank', fn ($row) => strtoupper($row->data_karyawan?->bank?->code_bank ?? $row->data_karyawan?->bank?->nama_bank ?? $row->data_karyawan?->bank?->name ?? '-'))
            ->addColumn('no_rekening', fn ($row) => $row->data_karyawan?->nomor_bank ?? '-')
            ->addColumn('atas_nama', fn ($row) => strtoupper($row->data_karyawan?->nama_rekening ?? '-'))
            ->addColumn('status_label', function ($row) {
                return match (strtolower(trim((string) $row->status))) {
                    'pending' => '<span class="badge badge-warning">BELUM DIPROSES</span>',
                    'approve' => '<span class="badge badge-success">AKTIF</span>',
                    'done' => '<span class="badge badge-primary">LUNAS</span>',
                    default => '<span class="badge badge-secondary">'.e(strtoupper((string) $row->status)).'</span>',
                };
            })
            ->addColumn('tanggal_transfer', fn ($row) => $row->disbursement?->transfer_date
                ? Carbon::parse($row->disbursement->transfer_date)->format('d/m/Y')
                : '-')
            ->addColumn('action', function ($row) {
                return strtolower(trim((string) $row->status)) === 'pending'
                    ? '<button type="button" class="btn btn-sm btn-outline-primary" onclick="editDisbursement('.$row->id.')" title="Edit status disbursement"><i class="mdi mdi-pencil-outline"></i></button>'
                    : '<span class="text-muted">-</span>';
            })
            ->rawColumns(['prioritas', 'doc_pk', 'ktp', 'selfie', 'status_label', 'action'])
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
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }

    private function evidencePath(string $directory, ?string $fileName): ?string
    {
        return filled($fileName) ? $directory.'/'.basename($fileName) : null;
    }

    public function approveDisbursement(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'transfer_date' => 'required|date',
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'declaration' => 'accepted',
        ]);

        $loan = pinjaman::with('data_karyawan')
            ->where('id', $request->integer('id'))
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

        $employee = $loan->data_karyawan;
        if (! $employee || ! filled($employee->id_nama_bank) || ! filled($employee->nomor_bank)
            || ! filled($employee->nama_rekening) || ! filled($loan->jumlah_yang_diterima)) {
            return response()->json([
                'status' => 422,
                'message' => 'Data rekening atau nominal pencairan karyawan belum lengkap.',
            ], 422);
        }

        $proof = $request->file('transfer_proof');
        $proofName = 'transfer-proof-'.$loan->id.'-'.time().'-'.Str::random(8).'.'.$proof->getClientOriginalExtension();
        $proofPath = $proof->storeAs('assets/disbursements', $proofName);

        DB::transaction(function () use ($loan, $employee, $request, $proofPath) {
            $disbursement = disbursement::where('pinjaman_id', $loan->id)->first() ?? new disbursement;
            $disbursement->pinjaman_id = $loan->id;
            $disbursement->admin_id = auth('admin')->id();
            $disbursement->bank_id = $employee->id_nama_bank;
            $disbursement->dana_yang_dicairkan = $loan->jumlah_yang_diterima;
            $disbursement->atas_nama = $employee->nama_rekening;
            $disbursement->nomor_rekening = $employee->nomor_bank;
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
}
