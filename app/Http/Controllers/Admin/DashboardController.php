<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\pinjaman;
use App\Models\User;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function dashboard()
    {
        // register
        $registerBulanIni = User::whereMonth('created_at', Carbon::now()->month)
            ->count();
        $registerBulanKemarin = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        $register = User::orderBy('created_at', 'desc')->take(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'Registrasi';
                return $item;
            });
        $pinjaman = pinjaman::orderBy('created_at', 'desc')->take(3)->with('data_karyawan')
            ->get()
            ->map(function ($item) {
                $item->type = 'Pinjaman Payroll';
                return $item;
            });

        $merged = $register->merge($pinjaman);
        $sorted = $merged->sortByDesc('created_at')->values();
        $dataFinal = [];
        foreach ($sorted as $data) {
            if ($data->type == 'Registrasi') {
                $name = $data->name;
            } else {
                $name = $data->data_karyawan->nama_karyawan ?? '-';
            }
            $dataFinal[] = [
                'type' => $data->type,
                'created_at' => $data->created_at->diffForHumans(),
                'name' => $name,
            ];
        }


        $persentase =  ($registerBulanIni - $registerBulanKemarin) * $registerBulanKemarin * 100 / 100;
        $register = [
            'persentase' => $persentase,
            'registerBulanIni' => $registerBulanIni,
            'activiti' => $dataFinal,
        ];
        // transaction
        return view('admin.dashboard.index', compact('register'));
    }
}
