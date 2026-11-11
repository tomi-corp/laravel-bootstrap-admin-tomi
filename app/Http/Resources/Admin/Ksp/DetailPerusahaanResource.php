<?php

namespace App\Http\Resources\Admin\Ksp;

use App\Http\Resources\Admin\Ksp\WilayahDesaResource;
use App\Http\Resources\Admin\Ksp\WilayahKabupatenResource;
use App\Http\Resources\Admin\Ksp\WilayahKecamatanResource;
use App\Models\kecamatan;
use App\Models\CompanyFeature;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\provinsi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPerusahaanResource extends JsonResource
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
            'pic_perusahaan' => $this->pic_perusahaan,
            'pic_internal' => $this->pic_internal,
            'nama' => $this->nama,
            'alamat_lengkap' => $this->alamat_lengkap,
            'telp' => $this->telp,
            'nomor_pks' => $this->nomor_pks,
            'company_features' => CompanyFeature::where('company_id', $this->id)
                ->get([
                    'feature_id',
                    'pks_number',
                    'pks_date',
                    'pks_expired_at',
                    'is_active',
                ])
                ->map(fn (CompanyFeature $companyFeature) => [
                    'feature_id' => $companyFeature->feature_id,
                    'pks_number' => $companyFeature->pks_number,
                    'pks_date' => $companyFeature->pks_date?->format('Y-m-d'),
                    'pks_expired_at' => $companyFeature->pks_expired_at?->format('Y-m-d'),
                    'is_active' => (bool) $companyFeature->is_active,
                ])
                ->values(),
            'status' => $this->status,
            'provinsi' => $this->provinsi->provinsi,
            'list_kabupaten' => WilayahKabupatenResource::collection($list_kota),
            'kabupaten' => $this->kabupaten->kabupaten,
            'list_kecamatan' => WilayahKecamatanResource::collection($list_kecamatan),
            'kecamatan' => $this->kecamatan->kecamatan,
            'list_desa' => WilayahDesaResource::collection($list_desa),
            'desa' => $this->desa->desa . '-' . $this->desa->kodepos,
            'payment_date' => $this->payment_date,
            'h_min_jatuh_tempo' => $this->h_min_jatuh_tempo,
            'active_profesional_fee' => $this->active_profesional_fee,
            'active_set_pinjaman' => $this->active_set_pinjaman,
            'active_tanggal' => $this->active_tanggal,
            'max_plafon' => $this->max_plafon,
            'email_perusahaan' => $this->email_perusahaan,
            'id_lender' => $this->id_lender,
        ];
    }
}
