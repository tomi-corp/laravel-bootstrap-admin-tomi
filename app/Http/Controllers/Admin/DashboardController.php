<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceEmail;
use App\Models\invoicing_pinjaman;
use App\Models\pinjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


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

    public function invoicee($cekkk, $id, $code_perusahaan)
    {
        if ($cekkk == "rLl52H1s1zl") {
            $getPeriod = invoicing_pinjaman::where('id', $id)
                ->with('perusahaan')
                ->first();
            if ($getPeriod) {
                if ($getPeriod->perusahaan->id_code_perusahaan == "#" . $code_perusahaan) {
                    $invoice = invoicing_pinjaman::where('id', $id)
                        ->with(['perusahaan' =>  function ($q) use ($getPeriod) {
                            $q->with(['pinjamanBerjalan' => function ($w) use ($getPeriod) {
                                $w->with(['jatuh_tempoSesuaiInvoice' =>  function ($e) use ($getPeriod) {
                                    $e->whereMonth('jatuh_tempo', Carbon::parse($getPeriod->period)->format('m'));
                                }, 'data_karyawan']);
                            }]);
                        }])
                        ->first();
                    return view("admin.ksp.invoicing.index", compact('invoice'));
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function test()
    {
        Carbon::setLocale('id');
        $waktu = Carbon::now();
        $carbonTahun = $waktu->format('Y');
        $bulan_romawi = $waktu->isoFormat('MM');
        dd($bulan_romawi);


        // sendOTPWhatsapp('6281296641453', '42432');
    }
}
