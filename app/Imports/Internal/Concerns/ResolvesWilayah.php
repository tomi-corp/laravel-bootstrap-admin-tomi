<?php

namespace App\Imports\Internal\Concerns;

use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\provinsi;

trait ResolvesWilayah
{
    /**
     * Cari ID wilayah berjenjang dari nama provinsi, kabupaten, kecamatan, desa, dan kode pos.
     * Pesan error ditambahkan ke $rowErrors jika ada wilayah yang tidak ditemukan.
     */
    protected function resolveWilayah(array $data, array &$rowErrors): ?array
    {
        $provinsi = provinsi::where('provinsi', $data['provinsi'])->first();
        if (! $provinsi) {
            $rowErrors[] = "Provinsi {$data['provinsi']} tidak ditemukan";

            return null;
        }

        $kabupaten = kota::where('provinsi', $provinsi->provinsi)->where('kabupaten', $data['kabupaten'])->first();
        if (! $kabupaten) {
            $rowErrors[] = "Kabupaten {$data['kabupaten']} tidak ditemukan di provinsi {$data['provinsi']}";

            return null;
        }

        $kecamatan = kecamatan::where('kabupaten', $kabupaten->kabupaten)->where('kecamatan', $data['kecamatan'])->first();
        if (! $kecamatan) {
            $rowErrors[] = "Kecamatan {$data['kecamatan']} tidak ditemukan di {$data['kabupaten']}";

            return null;
        }

        $desa = kelurahan::where('kecamatan', $kecamatan->kecamatan)
            ->where('desa', $data['desa'])
            ->where('kodepos', $data['kodepos'])
            ->first();
        if (! $desa) {
            $rowErrors[] = "Desa {$data['desa']} ({$data['kodepos']}) tidak ditemukan di kecamatan {$data['kecamatan']}";

            return null;
        }

        return [
            'provinsi' => $provinsi->id,
            'kabupaten' => $kabupaten->id,
            'kecamatan' => $kecamatan->id,
            'desa' => $desa->id,
        ];
    }
}
