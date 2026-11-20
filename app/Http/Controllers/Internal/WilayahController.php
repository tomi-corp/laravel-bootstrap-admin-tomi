<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use Illuminate\Http\Request;

/**
 * Pilihan wilayah berjenjang untuk form internal (perusahaan, lender).
 */
class WilayahController extends Controller
{
    public function cities(Request $req)
    {
        return kota::where('provinsi', $req->provinsi)->orderBy('kabupaten')->get();
    }

    public function districts(Request $req)
    {
        return kecamatan::where('kabupaten', $req->kabupaten)->orderBy('kecamatan')->get();
    }

    public function villages(Request $req)
    {
        return kelurahan::where('kecamatan', $req->kecamatan)->orderBy('desa')->get();
    }
}
