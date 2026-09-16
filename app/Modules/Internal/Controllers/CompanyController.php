<?php

namespace App\Modules\Internal\Controllers;

use App\Http\Controllers\Controller;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\lender;
use App\Models\ListPerusahaan;
use App\Models\provinsi;
use App\Modules\Internal\Requests\CompanyRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index()
    {
        return view('internal.companies.index', [
            'activeCompanies' => ListPerusahaan::where('status', 'active')->count(),
            'inactiveCompanies' => ListPerusahaan::where('status', 'nonactive')->count(),
            'lenders' => lender::orderBy('nama')->get(['id', 'nama']),
            'provinces' => provinsi::orderBy('provinsi')->get(['id', 'provinsi']),
        ]);
    }

    public function table()
    {
        $query = ListPerusahaan::query()
            ->with(['lender', 'provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->select('list_perusahaans.*');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('lender_name', fn (ListPerusahaan $company) => optional($company->lender)->nama ?? '-')
            ->addColumn('province_name', fn (ListPerusahaan $company) => optional($company->provinsi)->provinsi ?? '-')
            ->addColumn('city_name', fn (ListPerusahaan $company) => optional($company->kabupaten)->kabupaten ?? '-')
            ->addColumn('district_name', fn (ListPerusahaan $company) => optional($company->kecamatan)->kecamatan ?? '-')
            ->addColumn('village_name', function (ListPerusahaan $company) {
                if (! $company->desa) {
                    return '-';
                }

                return trim(($company->desa->desa ?? '-').' '.($company->desa->kodepos ?? ''));
            })
            ->addColumn('status_badge', function (ListPerusahaan $company) {
                $class = $company->status === 'active' ? 'badge-success' : 'badge-warning';

                return '<span class="badge '.$class.'">'.strtoupper($company->status).'</span>';
            })
            ->addColumn('action', function (ListPerusahaan $company) {
                return '<button type="button" class="btn btn-sm btn-primary mr-1" onclick="editCompany('.$company->id.')"><i class="fa-solid fa-pen"></i></button>'
                    .'<button type="button" class="btn btn-sm btn-danger" onclick="deleteCompany('.$company->id.')"><i class="fa-solid fa-trash-can"></i></button>';
            })
            ->filterColumn('lender_name', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
            ->filterColumn('province_name', fn ($query, $keyword) => $query->whereHas('provinsi', fn ($q) => $q->where('provinsi', 'like', "%{$keyword}%")))
            ->filterColumn('city_name', fn ($query, $keyword) => $query->whereHas('kabupaten', fn ($q) => $q->where('kabupaten', 'like', "%{$keyword}%")))
            ->filterColumn('district_name', fn ($query, $keyword) => $query->whereHas('kecamatan', fn ($q) => $q->where('kecamatan', 'like', "%{$keyword}%")))
            ->filterColumn('village_name', fn ($query, $keyword) => $query->whereHas('desa', fn ($q) => $q->where('desa', 'like', "%{$keyword}%")->orWhere('kodepos', 'like', "%{$keyword}%")))
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store(CompanyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['unique_code'] = $this->generateUniqueCode();
        $data['id_code_perusahaan'] = $this->generateCompanyCode();
        $data['max_plafon'] = $this->normalizeNullableNumber($data['max_plafon'] ?? null);

        ListPerusahaan::create($data);

        return response()->json([
            'message' => 'Perusahaan internal berhasil ditambahkan',
        ]);
    }

    public function show(ListPerusahaan $company): JsonResponse
    {
        return response()->json([
            'data' => $company->load(['lender', 'provinsi', 'kabupaten', 'kecamatan', 'desa']),
            'cities' => kota::where('provinsi', optional($company->provinsi)->provinsi)->orderBy('kabupaten')->get(['id', 'kabupaten']),
            'districts' => kecamatan::where('kabupaten', optional($company->kabupaten)->kabupaten)->orderBy('kecamatan')->get(['id', 'kecamatan']),
            'villages' => kelurahan::where('kecamatan', optional($company->kecamatan)->kecamatan)->orderBy('desa')->get(['id', 'desa', 'kodepos']),
        ]);
    }

    public function update(CompanyRequest $request, ListPerusahaan $company): JsonResponse
    {
        $data = $request->validated();
        $data['max_plafon'] = $this->normalizeNullableNumber($data['max_plafon'] ?? null);

        $company->update($data);

        return response()->json([
            'message' => 'Perusahaan internal berhasil diperbarui',
        ]);
    }

    public function destroy(ListPerusahaan $company): JsonResponse
    {
        $company->delete();

        return response()->json([
            'message' => 'Perusahaan internal berhasil dihapus',
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $province = provinsi::findOrFail($request->integer('province_id'));

        return response()->json(kota::where('provinsi', $province->provinsi)->orderBy('kabupaten')->get(['id', 'kabupaten']));
    }

    public function districts(Request $request): JsonResponse
    {
        $city = kota::findOrFail($request->integer('city_id'));

        return response()->json(kecamatan::where('kabupaten', $city->kabupaten)->orderBy('kecamatan')->get(['id', 'kecamatan']));
    }

    public function villages(Request $request): JsonResponse
    {
        $district = kecamatan::findOrFail($request->integer('district_id'));

        return response()->json(kelurahan::where('kecamatan', $district->kecamatan)->orderBy('desa')->get(['id', 'desa', 'kodepos']));
    }

    private function generateCompanyCode(): string
    {
        do {
            $code = '#TomiInternal-'.Carbon::now()->format('dmY').'-'.random_int(1000, 9999);
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

    private function normalizeNullableNumber(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
