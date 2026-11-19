<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Http\Requests\Admin\Ksp\AddKonfigurasiRequest;
use App\Http\Requests\Admin\Ksp\AmountPinjamanRequest;
use App\Http\Resources\Admin\Ksp\TenorPinjamanResource;
use App\Models\amount_pinjaman;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use App\Models\tenor_pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Konfigurasi pinjaman TomiDana per perusahaan: batas nominal & iuran (amount_pinjaman) dan tenor (tenor_pinjaman).
 */
class ConfigurationController extends Controller
{
    use ScopesCompanies;

    public function index(int $company)
    {
        $list_perusahaan = $this->findVisibleCompany($company)->load('amount_pinjaman');
        $max_plafon = list_karyawan::where('id_list_perusahaan', $list_perusahaan->id)->max('max_plafon') ?? 0;

        return view('internal.tomi-dana.configuration.index', compact('list_perusahaan', 'max_plafon'));
    }

    public function table(Request $req)
    {
        $company = $this->findVisibleCompany($req->id);
        $tenors = tenor_pinjaman::where('id_perusahaan', $company->id)->orderBy('tenor')->get();
        $iuranAnggota = amount_pinjaman::where('id_perusahaan', $company->id)->value('iuran_anggota') ?? 0;

        $percent = fn ($value) => $value.' %';
        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');

        return DataTables::of($tenors)
            ->addIndexColumn()
            ->addColumn('tenor', fn ($row) => $row->tenor.' bulan')
            ->addColumn('rate', fn ($row) => $percent($row->rate))
            ->addColumn('asuransi', fn ($row) => $percent($row->asuransi))
            ->addColumn('provisi', fn ($row) => $percent($row->provisi))
            ->addColumn('biaya_admin', fn ($row) => $percent($row->biaya_admin))
            ->addColumn('fee_tomi', fn ($row) => $percent($row->fee_tomi))
            ->addColumn('profesional_fee_1', fn ($row) => $percent($row->profesional_fee_1))
            ->addColumn('profesional_fee_2', fn ($row) => $percent($row->profesional_fee_2))
            ->addColumn('ksp', fn ($row) => $percent($row->ksp))
            ->addColumn('iuran_anggota', fn ($row) => $rupiah($row->tenor * $iuranAnggota))
            ->addColumn('biaya_layanan', fn ($row) => $rupiah($row->biaya_layanan))
            ->addColumn('minimal_nominal', fn ($row) => $rupiah($row->minimal_nominal))
            ->addColumn('maximal_nominal', fn ($row) => $rupiah($row->maximal_nominal))
            ->addColumn('action', fn ($row) => '<button class="btn btn-sm btn-primary mr-2" onclick="edit('.$row->id.')"><i class="mdi mdi-pencil"></i></button>'
                .'<button class="btn btn-sm btn-danger mr-2" onclick="deleteKonfigurasi('.$row->id.')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(AddKonfigurasiRequest $req)
    {
        $company = $this->findVisibleCompany($req->id);

        $tenor = new tenor_pinjaman;
        $tenor->id_perusahaan = $company->id;
        $this->fillTenor($tenor, $req, $company);
        $tenor->save();

        return response()->json([
            'status' => 200,
            'message' => 'Konfigurasi tenor berhasil ditambahkan.',
        ], 200);
    }

    public function show(Request $req)
    {
        $tenor = $this->findTenor($req->id);

        return response()->json([
            'status' => 200,
            'data' => new TenorPinjamanResource($tenor),
        ], 200);
    }

    public function update(AddKonfigurasiRequest $req)
    {
        $tenor = $this->findTenor($req->id);
        $this->fillTenor($tenor, $req, ListPerusahaan::findOrFail($tenor->id_perusahaan));
        $tenor->save();

        return response()->json([
            'status' => 200,
            'message' => 'Konfigurasi tenor berhasil diperbarui.',
        ], 200);
    }

    public function destroy(Request $req)
    {
        $this->findTenor($req->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Konfigurasi tenor berhasil dihapus.',
        ], 200);
    }

    public function amount(AmountPinjamanRequest $req)
    {
        $company = $this->findVisibleCompany($req->id);

        $amount = amount_pinjaman::firstOrNew(['id_perusahaan' => $company->id]);
        $amount->iuran_anggota = $req->iuran_anggota;
        $amount->minimal = $req->minimal;
        $amount->maximal = $req->maximal;
        $amount->maximal_persentase = $req->maximal_persentase;
        $amount->save();

        return response()->json([
            'status' => 200,
            'message' => 'Amount pinjaman berhasil diperbarui.',
        ], 200);
    }

    /**
     * Profesional fee & KSP hanya berlaku jika fitur profesional fee perusahaan aktif; selain itu disimpan 0.
     */
    private function fillTenor(tenor_pinjaman $tenor, Request $req, ListPerusahaan $company): void
    {
        $tenor->tenor = $req->tenor;
        $tenor->rate = $req->rate;
        $tenor->asuransi = $req->asuransi;
        $tenor->provisi = $req->provisi;
        $tenor->biaya_admin = $req->biaya_admin;
        $tenor->fee_tomi = $req->fee_tomi;
        $tenor->biaya_layanan = $req->biaya_layanan;
        $tenor->minimal_nominal = $req->minimal_nominal;
        $tenor->maximal_nominal = $req->maximal_nominal;

        $professionalFeeActive = $company->active_profesional_fee === 'active';
        $tenor->profesional_fee_1 = $professionalFeeActive ? $req->profesional_fee_1 : 0;
        $tenor->profesional_fee_2 = $professionalFeeActive ? $req->profesional_fee_2 : 0;
        $tenor->ksp = $professionalFeeActive ? $req->ksp : 0;
    }

    private function findTenor(int|string|null $id): tenor_pinjaman
    {
        $tenor = tenor_pinjaman::findOrFail((int) $id);
        $this->authorizeCompanyId($tenor->id_perusahaan);

        return $tenor;
    }
}
