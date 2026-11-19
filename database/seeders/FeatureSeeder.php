<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

/**
 * Data fitur/produk TOMI yang dipakai di PKS perusahaan.
 * Aman dijalankan berulang: data dicocokkan berdasarkan kode.
 *
 * php artisan db:seed --class=FeatureSeeder
 */
class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['code' => 'tomidana', 'name' => 'TomiDana', 'description' => 'Pinjaman dana untuk karyawan perusahaan'],
            ['code' => 'tomidanaplus', 'name' => 'TomiDana Plus', 'description' => 'Pinjaman dana plus untuk karyawan perusahaan'],
            ['code' => 'tomigaji', 'name' => 'TomiGaji', 'description' => 'Pembiayaan gaji (salary financing)'],
            ['code' => 'tomiinvoice', 'name' => 'TomiInvoice', 'description' => 'Pembiayaan invoice perusahaan'],
            ['code' => 'tomigawai', 'name' => 'TomiGawai', 'description' => 'Pembiayaan gawai untuk karyawan'],
        ];

        foreach ($features as $feature) {
            Feature::withTrashed()->updateOrCreate(
                ['code' => $feature['code']],
                $feature + ['status' => 'active', 'deleted_at' => null],
            );
        }
    }
}
