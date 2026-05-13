<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class NewDashboardController extends Controller
{
    public function index()
    {
        /**
         * Total perusahaan
         */
        $totalPerusahaan = DB::table('perusahaans')->count();

        /**
         * Total karyawan yang punya pinjaman pending.
         * distinct dipakai agar 1 karyawan tidak dihitung berkali-kali
         * kalau dia punya lebih dari 1 data pinjaman.
         */
        $karyawanPinjamanPending = DB::table('pinjamans')
            ->where('status', 'pending')
            ->distinct('id_karyawan')
            ->count('id_karyawan');

        /**
         * Total karyawan yang pinjamannya approved.
         */
        $karyawanPinjamanApproved = DB::table('pinjamans')
            ->where('status', 'approved')
            ->distinct('id_karyawan')
            ->count('id_karyawan');

        /**
         * Total karyawan yang pinjamannya rejected / ditolak.
         * Kalau status di database kamu namanya "rejected", pakai rejected.
         * Kalau namanya "ditolak", ganti menjadi ditolak.
         */
        $karyawanPinjamanRejected = DB::table('pinjamans')
            ->where('status', 'rejected')
            ->distinct('id_karyawan')
            ->count('id_karyawan');

        /**
         * Total semua karyawan.
         */
        $totalKaryawan = DB::table('list_karyawans')->count();

        return view('admin.dashboard.new-index', compact(
            'totalPerusahaan',
            'totalKaryawan',
            'karyawanPinjamanPending',
            'karyawanPinjamanApproved',
            'karyawanPinjamanRejected'
        ));
    }
}
