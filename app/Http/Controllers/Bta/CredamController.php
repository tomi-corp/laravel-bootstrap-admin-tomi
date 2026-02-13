<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Models\nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class CredamController extends Controller
{
    public function credamIndex()
    {
        return view('bta.credam.index');
    }

    public function credamReview($id)
    {
        $nasabah = nasabah::where('id', $id)->first();
        if (!isset($nasabah)) {
            abort(404);
        }

        // Urutan sesuai Migration Database
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

        return view('bta.credam.review.index', compact('nasabah', 'safeUrls'));
    }


    public function credamTable()
    {
        $nasabah = nasabah::where('position', 'credam')
            ->where('status', 'pending')
            ->select([
                'id',
                'no_ktp',              // pastikan kolom ini ada di DB
                'nama_lengkap',
                'tanggal_lahir',
                'jenis_kelamin',
                'no_telp',
                'status_perkawinan',
                'nama_perusahaan',
                'status_pegawai',
                // tambahkan kolom lain jika perlu
            ])

            // opsional: filter hanya nasabah dengan status tertentu
            // ->where('status', 'pending')
            ->get();

        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('bta.credam.review', $row->id) . '" class="btn btn-success btn-sm"><i class="mdi mdi-magnify"></i></a>
                ';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function credamApprove(Request $req)
    {
        $nasabah = nasabah::where("no_ktp", $req->no_ktp)->first();
        $nasabah->position = 'credam';
        $nasabah->status = 'approve';
        $nasabah->save();
        return response()->json([
            'message' => 'Data nasabah berhasil menjadi kredit berjalan.'
        ]);
    }
}
