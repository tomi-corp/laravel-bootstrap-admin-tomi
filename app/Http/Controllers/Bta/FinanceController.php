<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Models\nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class FinanceController extends Controller
{
    public function index()
    {
        return view('bta.finance.index', [
            'pageTitle' => 'Finance - Belum Upload Bukti',
            'tableRoute' => 'bta.finance.table',
            'mode' => 'pending',
        ]);
    }

    public function uploadedIndex()
    {
        return view('bta.finance.index', [
            'pageTitle' => 'Finance - Sudah Upload Bukti',
            'tableRoute' => 'bta.finance.uploaded.table',
            'mode' => 'uploaded',
        ]);
    }

    public function table()
    {
        return $this->financeTable('pending');
    }

    public function uploadedTable()
    {
        return $this->financeTable('uploaded');
    }

    private function financeTable(string $uploadStatus)
    {
        $columns = [
            'id',
            'no_ktp',
            'nama_lengkap',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_telp',
            'status_perkawinan',
            'nama_perusahaan',
            'status_pegawai',
            'jangka_waktu',
            'mulai_pinjaman',
            'akhir_pinjaman',
        ];

        $hasTanggalDisbursement = Schema::hasColumn('nasabahs', 'tanggal_disbursement');
        $hasBuktiDisbursement = Schema::hasColumn('nasabahs', 'bukti_disbursement');

        if ($hasTanggalDisbursement) {
            $columns[] = 'tanggal_disbursement';
        }

        if ($hasBuktiDisbursement) {
            $columns[] = 'bukti_disbursement';
        }

        $nasabah = nasabah::where('position', 'credam')
            ->where('status', 'approve');

        if ($hasBuktiDisbursement) {
            if ($uploadStatus === 'uploaded') {
                $nasabah->whereNotNull('bukti_disbursement')
                    ->where('bukti_disbursement', '!=', '');
            } else {
                $nasabah->where(function ($query) {
                    $query->whereNull('bukti_disbursement')
                        ->orWhere('bukti_disbursement', '');
                });
            }
        } elseif ($uploadStatus === 'uploaded') {
            $nasabah->whereRaw('1 = 0');
        }

        $nasabah = $nasabah->select($columns)->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($hasTanggalDisbursement, $hasBuktiDisbursement, $uploadStatus) {
                if ($uploadStatus === 'uploaded') {
                    $tanggalDisbursement = $hasTanggalDisbursement && $row->tanggal_disbursement
                        ? date('Y-m-d', strtotime($row->tanggal_disbursement))
                        : '';
                    $mulaiPinjaman = $row->mulai_pinjaman ? date('Y-m-d', strtotime($row->mulai_pinjaman)) : '';
                    $akhirPinjaman = $row->akhir_pinjaman ? date('Y-m-d', strtotime($row->akhir_pinjaman)) : '';

                    return '
                        <button type="button"
                            class="btn btn-info btn-sm btn-view-proof"
                            title="Lihat Bukti Bayar"
                            data-nama="' . e($row->nama_lengkap) . '"
                            data-tanggal-disbursement="' . e($tanggalDisbursement) . '"
                            data-mulai-pinjaman="' . e($mulaiPinjaman) . '"
                            data-akhir-pinjaman="' . e($akhirPinjaman) . '"
                            data-url="' . e(route('bta.finance.proof', $row->id)) . '">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    ';
                }

                $mulaiPinjaman = $row->mulai_pinjaman ? date('Y-m-d', strtotime($row->mulai_pinjaman)) : '';
                $akhirPinjaman = $row->akhir_pinjaman ? date('Y-m-d', strtotime($row->akhir_pinjaman)) : '';
                $tanggalDisbursement = $hasTanggalDisbursement && $row->tanggal_disbursement
                    ? date('Y-m-d', strtotime($row->tanggal_disbursement))
                    : '';
                $hasBukti = $hasBuktiDisbursement && !empty($row->bukti_disbursement);

                return '
                    <button type="button"
                        class="btn btn-success btn-sm btn-disbursement"
                        title="Upload Bukti Disbursement"
                        data-id="' . e($row->id) . '"
                        data-nama="' . e($row->nama_lengkap) . '"
                        data-jangka-waktu="' . e($row->jangka_waktu) . '"
                        data-mulai-pinjaman="' . e($mulaiPinjaman) . '"
                        data-akhir-pinjaman="' . e($akhirPinjaman) . '"
                        data-tanggal-disbursement="' . e($tanggalDisbursement) . '"
                        data-has-bukti="' . e($hasBukti ? '1' : '0') . '">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function proof($id)
    {
        $nasabah = nasabah::where('id', $id)
            ->where('position', 'credam')
            ->where('status', 'approve')
            ->first();

        if (!$nasabah || empty($nasabah->bukti_disbursement)) {
            abort(404);
        }

        $path = 'bta/' . $nasabah->bukti_disbursement;

        if (!Storage::exists($path)) {
            abort(404);
        }

        return response()->file(Storage::path($path), [
            'Content-Type' => Storage::mimeType($path),
        ]);
    }

    public function review($id)
    {
        $nasabah = nasabah::where('id', $id)
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'bank',
                'dati'
            ])
            ->where('position', 'credam')
            ->where('status', 'approve')
            ->first();

        if (!isset($nasabah)) {
            abort(404);
        }

        $documents = [
            'ktp'               => $nasabah->ktp,
            'npwp'              => $nasabah->npwp,
            'selfie'            => $nasabah->selfie,
            'kk'                => $nasabah->kk,
            'id_card'           => $nasabah->id_card,
            'sk_karyawan'       => $nasabah->sk_karyawan,
            'slip_gaji'         => $nasabah->slip_gaji,
            'buku_tabungan'     => $nasabah->buku_tabungan,
            'mutasi_rekening'   => $nasabah->mutasi_rekening,
            'form_slik'         => $nasabah->form_slik,
            'form_kredit'       => $nasabah->form_kredit,
            'form_bta'          => $nasabah->form_bta,
            'form_checklist'    => $nasabah->form_checklist,
            'foto_akad_kredit'  => $nasabah->foto_akad_kredit,
            'pk_tanda_tangan'   => $nasabah->pk_tanda_tangan,
            'video_interview'   => $nasabah->video_interview,
            'video_asuransi'    => $nasabah->video_asuransi,
        ];

        $safeUrls = [];
        foreach ($documents as $key => $fileName) {
            if ($fileName) {
                $payload = [
                    'file_path' => 'bta/' . $fileName,
                    'expires_at' => now()->addHours(24)->timestamp,
                ];

                $encryptedToken = Crypt::encrypt(json_encode($payload));
                $tokenForUrl = base64_encode($encryptedToken);

                $safeUrls[$key] = route('bta.secure.file.download', ['token' => $tokenForUrl]);
            } else {
                $safeUrls[$key] = null;
            }
        }

        return view('bta.credam.review.index', [
            'nasabah' => $nasabah,
            'safeUrls' => $safeUrls,
            'reviewTitle' => 'Review Finance',
            'reviewMenuTitle' => 'Finance',
            'reviewIndexRoute' => 'bta.finance.index',
            'reviewApproveRoute' => 'bta.finance.approve',
        ]);
    }

    public function approve(Request $req)
    {
        $validated = $req->validate([
            'id' => 'required|exists:nasabahs,id',
            'tanggal_disbursement' => 'required|date',
            'bukti_disbursement' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'mulai_pinjaman' => 'required|date',
            'akhir_pinjaman' => 'required|date|after_or_equal:mulai_pinjaman',
        ], [
            'tanggal_disbursement.required' => 'Tanggal disbursement wajib diisi.',
            'bukti_disbursement.required' => 'Bukti disbursement wajib diupload.',
            'bukti_disbursement.mimes' => 'Bukti disbursement harus berupa JPG, PNG, atau PDF.',
            'mulai_pinjaman.required' => 'Mulai pinjaman wajib diisi.',
            'akhir_pinjaman.required' => 'Akhir pinjaman wajib diisi.',
            'akhir_pinjaman.after_or_equal' => 'Akhir pinjaman harus sama atau setelah mulai pinjaman.',
        ]);

        $nasabah = nasabah::where('id', $validated['id'])
            ->where('position', 'credam')
            ->where('status', 'approve')
            ->first();

        if (!$nasabah) {
            return response()->json([
                'message' => 'Data nasabah tidak ditemukan.'
            ], 404);
        }

        $file = $req->file('bukti_disbursement');
        $filename = time() . '_' . uniqid() . '_bukti_disbursement.' . $file->getClientOriginalExtension();
        $file->storeAs('bta', $filename);

        if (!empty($nasabah->bukti_disbursement)) {
            Storage::delete('bta/' . $nasabah->bukti_disbursement);
        }

        $nasabah->tanggal_disbursement = $validated['tanggal_disbursement'];
        $nasabah->bukti_disbursement = $filename;
        $nasabah->mulai_pinjaman = $validated['mulai_pinjaman'];
        $nasabah->akhir_pinjaman = $validated['akhir_pinjaman'];
        $nasabah->position = 'credam';
        $nasabah->status = 'approve';
        $nasabah->save();

        return response()->json([
            'message' => 'Data nasabah berhasil diproses finance.'
        ]);
    }
}
