<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Models\bank;
use App\Models\dati;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\provinsi;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function provinsi()
    {
        $provinsi = provinsi::get();
        return $provinsi;
    }

    public function kota(Request $req)
    {
        $kota = kota::where('provinsi', $req->nama)->get();
        return $kota;
    }

    public function kotaEdit(Request $req)
    {
        $provinsi = provinsi::where('id', $req->id)->first();
        $kota = kota::where('provinsi', $provinsi->provinsi)->get();
        return $kota;
    }

    public function kecamatan(Request $req)
    {
        $kecamatan = kecamatan::where('kabupaten', $req->nama)->get();
        return $kecamatan;
    }

    public function kecamatanEdit(Request $req)
    {
        $kota = kota::where('id', $req->id)->first();
        $kecamatan = kecamatan::where('kabupaten', $kota->kabupaten)->get();
        $dati = dati::where('kabupaten', $kota->kabupaten)->first();
        return response()->json([
            'status' => 200,
            'kecamatan' => $kecamatan,
            'dati' => $dati
        ], 200);
    }

    public function kelurahan(Request $req)
    {
        $kelurahan = kelurahan::where('kecamatan', $req->nama)->get();
        return $kelurahan;
    }

    public function kelurahanEdit(Request $req)
    {
        $kecamatan = kecamatan::where('id', $req->id)->first();
        $kelurahan = kelurahan::where('kecamatan', $kecamatan->kecamatan)->get();
        return $kelurahan;
    }

    public function kodepos(Request $req)
    {
        $kodepos = kelurahan::where('kecamatan', $req->kecamatan)
            ->where('desa', $req->desa)
            ->get();
        return $kodepos;
    }

    public function dati(Request $req)
    {
        $dati = dati::where('kabupaten', $req->nama)->get();
        return $dati;
    }

    public function bank(Request $req)
    {
        $bank = bank::get();
        return $bank;
    }



    // Kota baru
    public function kotaNew(Request $req)
    {
        $provinsi = provinsi::where('id', $req->id)->first();
        $kota = kota::where('provinsi', $provinsi->provinsi)->get();
        return $kota;
    }

    public function kecamatanNew(Request $req)
    {
        $kota = kota::where('id', $req->id)->first();
        $kecamatan = kecamatan::where('kabupaten', $kota->kabupaten)->get();
        return $kecamatan;
    }

    public function datiNew(Request $req)
    {
        $kota = kota::where('id', $req->id)->first();
        $dati = dati::where('kabupaten', $kota->kabupaten)->first();
        return $dati;
    }

    public function kelurahanNew(Request $req)
    {
        $kecamatan = kecamatan::where('id', $req->id)->first();
        $kelurahan = kelurahan::where('kecamatan', $kecamatan->kecamatan)->get();
        return $kelurahan;
    }

    public function kodeposNew(Request $req)
    {
        $kelurahan = kelurahan::where('id', $req->id)->first();
        return $kelurahan;
    }
}
