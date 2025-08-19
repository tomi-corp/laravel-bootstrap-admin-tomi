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
        $sekarang = Carbon::now()->format('Y-m-d');
        $sekarangUdahDikurangi =  Carbon::parse($sekarang)->subDays(5);
        $invoicings = invoicing_pinjaman::where('period', '<=', $sekarangUdahDikurangi)
            ->where('status', 'belum_lunas')
            ->with('perusahaan')
            ->get();

        foreach ($invoicings as $invoicing) {
            $getPeriod = invoicing_pinjaman::where('id', $invoicing->id)->first();
            $invoice = invoicing_pinjaman::where('id', $invoicing->id)
                ->with(['perusahaan' =>  function ($q) use ($getPeriod) {
                    $q->with(['pinjamanBerjalan' => function ($w) use ($getPeriod) {
                        $w->with(['jatuh_tempoSesuaiInvoice' =>  function ($e) use ($getPeriod) {
                            $e->whereMonth('jatuh_tempo', Carbon::parse($getPeriod->period)->format('m'));
                        }, 'data_karyawan']);
                    }]);
                }])
                ->first();

            $link = url(env('APP_URL')) . '/sIztJyJPErLl52H1s1zlXu5q5z5k6mMwBYC6VQlGQ65eMNGSB5u/rLl52H1s1zl/' . $invoice->id . '/' . substr($invoice->perusahaan->id_code_perusahaan, 1);
            $data = [
                'subject' => 'Pemberitahuan Invoice',
                'title' => 'Pemberitahuan Invoice',
                // 'to' => $invoice->perusahaan->email_perusahaan,
                'to' => 'imamrovid9@gmail.com',
                'link' => $link,
                'invoice' => $invoice
            ];
            try {
                Mail::to($data['to'])->send(new InvoiceEmail($data));
            } catch (\Throwable $th) {
                Log::channel('cronjob')->info($sekarang . ' Error : ' . $th);
            }
        }
        Log::channel('cronjob')->info($sekarang . ' Cronjob Invoice telah dijalankan');
    }
}
