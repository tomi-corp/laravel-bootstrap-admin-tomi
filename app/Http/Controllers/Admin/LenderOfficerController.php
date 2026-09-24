<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LenderOfficerRequest;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\lender;
use App\Models\LenderOfficer;
use App\Models\provinsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LenderOfficerController extends Controller
{
    private const POSITIONS = [
        'chairperson' => 'Ketua',
        'vice_chairperson' => 'Wakil Ketua',
        'secretary' => 'Sekretaris',
        'treasurer' => 'Bendahara',
        'supervisor' => 'Pengawas',
        'other' => 'Lainnya',
    ];

    public function index(): View
    {
        $officers = LenderOfficer::query()
            ->with(['lender:id,nama', 'province:id,provinsi', 'city:id,kabupaten'])
            ->latest()
            ->get();

        return view('admin.lender-officers.index', [
            'officers' => $officers,
            'positions' => self::POSITIONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.lender-officers.create', $this->formData(new LenderOfficer()));
    }

    public function store(LenderOfficerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['ktp_file']);

        if ($request->hasFile('ktp_file')) {
            $data['ktp_file_path'] = $request->file('ktp_file')->store('lender-officers/ktp', 'public');
        }

        LenderOfficer::create($data);

        return redirect()->route('admin.lender-officers.index')->with('success', 'Pengurus lender berhasil ditambahkan.');
    }

    public function show(LenderOfficer $lender_officer): View
    {
        $lender_officer->load(['lender', 'province', 'city', 'district', 'village']);

        return view('admin.lender-officers.show', [
            'officer' => $lender_officer,
            'positions' => self::POSITIONS,
        ]);
    }

    public function edit(LenderOfficer $lender_officer): View
    {
        return view('admin.lender-officers.edit', $this->formData($lender_officer));
    }

    public function update(LenderOfficerRequest $request, LenderOfficer $lender_officer): RedirectResponse
    {
        $data = $request->validated();
        unset($data['ktp_file']);

        if ($request->hasFile('ktp_file')) {
            $this->deleteFile($lender_officer->ktp_file_path);
            $data['ktp_file_path'] = $request->file('ktp_file')->store('lender-officers/ktp', 'public');
        }

        $lender_officer->update($data);

        return redirect()->route('admin.lender-officers.index')->with('success', 'Pengurus lender berhasil diperbarui.');
    }

    public function destroy(LenderOfficer $lender_officer): RedirectResponse
    {
        $lender_officer->delete();

        return redirect()->route('admin.lender-officers.index')->with('success', 'Pengurus lender berhasil dihapus.');
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

    private function formData(LenderOfficer $officer): array
    {
        $officer->loadMissing(['province', 'city', 'district']);

        return [
            'officer' => $officer,
            'lenders' => lender::query()->orderBy('nama')->get(['id', 'nama']),
            'positions' => self::POSITIONS,
            'provinces' => provinsi::orderBy('provinsi')->get(['id', 'provinsi']),
            'cities' => $officer->province ? kota::where('provinsi', $officer->province->provinsi)->orderBy('kabupaten')->get(['id', 'kabupaten']) : collect(),
            'districts' => $officer->city ? kecamatan::where('kabupaten', $officer->city->kabupaten)->orderBy('kecamatan')->get(['id', 'kecamatan']) : collect(),
            'villages' => $officer->district ? kelurahan::where('kecamatan', $officer->district->kecamatan)->orderBy('desa')->get(['id', 'desa', 'kodepos']) : collect(),
        ];
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
