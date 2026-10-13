<?php

namespace App\Http\Resources\Admin\Lender;

use App\Http\Resources\Admin\Ksp\WilayahDesaResource;
use App\Http\Resources\Admin\Ksp\WilayahKabupatenResource;
use App\Http\Resources\Admin\Ksp\WilayahKecamatanResource;
use App\Models\Admin;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailLenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // kabupaten / kota
        $list_kota = kota::where('provinsi',  $this->provinsi->provinsi)->get();
        // kecamatan
        $list_kecamatan = kecamatan::where('kabupaten', $this->kabupaten->kabupaten)->get();
        // list_desa
        $list_desa = kelurahan::where('kecamatan', $this->kecamatan->kecamatan)->get();

        return [
            'pic_lender' => $this->pic_lender,
            'pic_internal' => $this->pic_internal,
            'pic_forms' => $this->picForms(),
            'nama' => $this->nama,
            'email_lender' => $this->email_lender,
            'telp' => $this->telp,
            'nomor_pks' => $this->nomor_pks,
            'status' => $this->status,
            'provinsi' => $this->provinsi->provinsi,
            'alamat_lengkap' => $this->alamat_lengkap,
            'list_kabupaten' => WilayahKabupatenResource::collection($list_kota),
            'kabupaten' => $this->kabupaten->kabupaten,
            'list_kecamatan' => WilayahKecamatanResource::collection($list_kecamatan),
            'kecamatan' => $this->kecamatan->kecamatan,
            'list_desa' => WilayahDesaResource::collection($list_desa),
            'desa' => $this->desa->desa . '-' . $this->desa->kodepos,
            'id_bank' => $this->id_bank,
            'nama_rekening' => $this->nama_rekening,
            'no_rekening' => $this->no_rekening,
            'color' => $this->color,
        ];
    }

    private function picForms(): array
    {
        return collect([
            'pic_lender' => $this->pic_lender,
            'pic_internal' => $this->pic_internal,
        ])->map(function (?string $name, string $prefix) {
            $admin = Admin::query()
                ->where('lender_id', $this->id)
                ->where('username', "{$prefix}_lender_{$this->id}")
                ->first();

            return [
                'name' => $name,
                'national_id_number' => $admin?->nik,
                'birth_place' => $admin?->birth_place,
                'birth_date' => $admin?->birth_date ? substr((string) $admin->birth_date, 0, 10) : null,
                'gender' => $admin?->gender,
                'blood_type' => $admin?->blood_type,
                'address' => $admin?->address,
                'rt' => $admin?->rt,
                'rw' => $admin?->rw,
                'province_id' => $admin?->wilayah_provinsi_net_id,
                'city_id' => $admin?->wilayah_kabupaten_net_id,
                'district_id' => $admin?->wilayah_kecamatan_net_id,
                'village_id' => $admin?->wilayah_desa_net_id,
                'postal_code' => $admin?->postal_code,
                'religion' => $this->formReligion($admin?->religion),
                'marital_status' => $admin?->marital_status,
                'occupation' => $admin?->occupation,
                'nationality' => $admin?->nationality,
                'phone_number' => $admin?->phone,
                'email' => $admin?->email,
            ];
        })->all();
    }

    private function formReligion(?string $religion): ?string
    {
        return match ($religion) {
            'kristen' => 'christian',
            'katolik' => 'catholic',
            'buddha' => 'buddhist',
            'konghucu' => 'confucian',
            default => $religion,
        };
    }
}
