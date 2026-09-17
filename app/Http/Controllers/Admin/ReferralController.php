<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Referral\StoreReferralRequest;
use App\Http\Requests\Admin\Referral\UpdateReferralRequest;
use App\Models\bank;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\provinsi;
use App\Models\Referral;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ReferralController extends Controller
{
    public function index()
    {
        $banks = bank::orderBy('code_bank')->get();
        $provinces = provinsi::orderBy('provinsi')->get(['id', 'provinsi']);

        return view('admin.referral.index', compact('banks', 'provinces'));
    }

    public function table()
    {
        $referrals = Referral::with(['bank', 'province', 'city', 'district', 'village'])->orderByDesc('id');

        return DataTables::eloquent($referrals)
            ->addIndexColumn()
            ->addColumn('bank_name', fn (Referral $referral) => $referral->bank?->code_bank ?? '-')
            ->addColumn('status_badge', function (Referral $referral) {
                $class = $referral->is_active ? 'badge-success' : 'badge-warning';
                $label = $referral->is_active ? 'ACTIVE' : 'INACTIVE';

                return '<span class="badge '.$class.'">'.$label.'</span>';
            })
            ->addColumn('created_at_formatted', function (Referral $referral) {
                return $referral->created_at ? Carbon::parse($referral->created_at)->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('action', function (Referral $referral) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="editReferral('.$referral->id.')"><i class="fa-solid fa-pen"></i></button>'
                    .'<button class="btn btn-sm btn-danger mr-2" onclick="deleteReferral('.$referral->id.')"><i class="fa-solid fa-trash-can"></i></button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->only([
                'DT_RowIndex',
                'created_at_formatted',
                'national_id_number',
                'name',
                'phone_number',
                'email',
                'bank_name',
                'bank_account_number',
                'status_badge',
                'action',
            ])
            ->toJson();
    }

    public function store(StoreReferralRequest $request): JsonResponse
    {
        $data = $this->payload($request);

        if ($request->hasFile('id_card_file')) {
            $data['id_card_file'] = $request->file('id_card_file')->store('referrals/id-cards', 'public');
        }

        if ($request->hasFile('bank_book_file')) {
            $data['bank_book_file'] = $request->file('bank_book_file')->store('referrals/bank-books', 'public');
        }

        Referral::create($data);

        return response()->json([
            'status' => 200,
            'message' => 'Referral berhasil ditambahkan',
        ]);
    }

    public function detail(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['required', 'exists:referrals,id'],
        ]);

        $referral = Referral::findOrFail($request->id);

        return response()->json([
            'status' => 200,
            'data' => $referral,
            'cities' => $referral->province ? kota::where('provinsi', $referral->province->provinsi)->orderBy('kabupaten')->get(['id', 'kabupaten']) : [],
            'districts' => $referral->city ? kecamatan::where('kabupaten', $referral->city->kabupaten)->orderBy('kecamatan')->get(['id', 'kecamatan']) : [],
            'villages' => $referral->district ? kelurahan::where('kecamatan', $referral->district->kecamatan)->orderBy('desa')->get(['id', 'desa', 'kodepos']) : [],
            'id_card_url' => $referral->id_card_file ? Storage::disk('public')->url($referral->id_card_file) : null,
            'bank_book_url' => $referral->bank_book_file ? Storage::disk('public')->url($referral->bank_book_file) : null,
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

    public function update(UpdateReferralRequest $request): JsonResponse
    {
        $referral = Referral::findOrFail($request->id);
        $data = $this->payload($request);

        if ($request->hasFile('id_card_file')) {
            $this->deleteFile($referral->id_card_file);
            $data['id_card_file'] = $request->file('id_card_file')->store('referrals/id-cards', 'public');
        }

        if ($request->hasFile('bank_book_file')) {
            $this->deleteFile($referral->bank_book_file);
            $data['bank_book_file'] = $request->file('bank_book_file')->store('referrals/bank-books', 'public');
        }

        $referral->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Referral berhasil diperbarui',
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['required', 'exists:referrals,id'],
        ]);

        Referral::findOrFail($request->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Referral berhasil dihapus',
        ]);
    }

    private function payload(Request $request): array
    {
        return [
            'national_id_number' => $request->national_id_number,
            'name' => $request->name,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'blood_type' => $request->blood_type,
            'address' => $request->address,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id,
            'postal_code' => $request->postal_code,
            'religion' => $request->religion,
            'marital_status' => $request->marital_status,
            'occupation' => $request->occupation,
            'nationality' => $request->nationality ?: 'WNI',
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'bank_id' => $request->bank_id,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'is_active' => $request->boolean('is_active'),
            'notes' => $request->notes,
        ];
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
