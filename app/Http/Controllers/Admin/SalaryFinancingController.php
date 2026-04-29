<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryFinancingController extends Controller
{
    public function index()
    {
        /*
    |--------------------------------------------------------------------------
    | Contoh Data Perusahaan
    |--------------------------------------------------------------------------
    | Ini hanya data dummy, bukan dari database.
    |--------------------------------------------------------------------------
    */
        $perusahaans = [
            [
                'id' => 1,
                'nama' => 'PT Bintang Pratama Delindo',
                'status' => 'active',
                'pic_perusahaan' => 'Budi Santoso',
                'pic_internal' => 'Rina Marlina',
                'email_perusahaan' => 'hrd@bintangpratama.co.id',
                'telp' => '0215551234',
                'provinsi' => 'DKI Jakarta',
                'kabupaten' => 'Jakarta Selatan',
                'kecamatan' => 'Setiabudi',
                'desa' => 'Karet',
                'alamat_lengkap' => 'Jl. Contoh Raya No. 10',
                'payment_date' => 25,
                'h_min_jatuh_tempo' => 3,
                'nomor_pks' => '012/KSPDUMI-FIT/VIII/2025',
                'active_profesional_fee' => 'active',
                'active_set_pinjaman' => 'active',
                'active_tanggal' => 1,
            ],
            [
                'id' => 2,
                'nama' => 'PT Sinar Digital Nusantara',
                'status' => 'nonactive',
                'pic_perusahaan' => 'Andi Wijaya',
                'pic_internal' => 'Dewi Lestari',
                'email_perusahaan' => 'finance@sinardigital.co.id',
                'telp' => '0217778899',
                'provinsi' => 'Banten',
                'kabupaten' => 'Tangerang',
                'kecamatan' => 'Cikupa',
                'desa' => 'Sukamulya',
                'alamat_lengkap' => 'Jl. Industri Raya No. 88',
                'payment_date' => 27,
                'h_min_jatuh_tempo' => 5,
                'nomor_pks' => '021/KSPDUMI-SDN/IX/2025',
                'active_profesional_fee' => 'nonactive',
                'active_set_pinjaman' => 'nonactive',
                'active_tanggal' => null,
            ],
        ];

        /*
    |--------------------------------------------------------------------------
    | Hitung Jumlah Status dari Array
    |--------------------------------------------------------------------------
    */
        $perusahaanAktif = collect($perusahaans)
            ->where('status', 'active')
            ->count();

        $perusahaanTidakAktif = collect($perusahaans)
            ->where('status', 'nonactive')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Contoh List Provinsi dari Array
    |--------------------------------------------------------------------------
    */
        $provinsis = collect([
            [
                'provinsi' => 'DKI Jakarta',
            ],
            [
                'provinsi' => 'Banten',
            ],
            [
                'provinsi' => 'Jawa Barat',
            ],
            [
                'provinsi' => 'Jawa Tengah',
            ],
            [
                'provinsi' => 'Jawa Timur',
            ],
        ]);

        return view('admin.ksp.perusahaan.index', [
            'perusahaans' => $perusahaans,
            'perusahaanAktif' => $perusahaanAktif,
            'perusahaanTidakAktif' => $perusahaanTidakAktif,
            'provinsis' => $provinsis,
        ]);
    }
}
