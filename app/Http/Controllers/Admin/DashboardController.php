<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceEmail;
use App\Models\invoicing_pinjaman;
use App\Models\User;
use App\Models\xenditApi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;



class DashboardController extends Controller
{
    public function dashboard()
    {
        // register
        $registerBulanIni = User::whereMonth('created_at', Carbon::now()->month)
            ->count();
        $registerBulanKemarin = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        $persentase =  ($registerBulanIni - $registerBulanKemarin) * $registerBulanKemarin * 100 / 100;
        $register = [
            'persentase' => $persentase,
            'registerBulanIni' => $registerBulanIni,
        ];
        // transaction
        return view('admin.dashboard.index', compact('register'));
    }

    public function test()
    {
        $sekarang = Carbon::now()->format('Y-m-d');
        $sekarangUdahDikurangi =  Carbon::parse($sekarang)->subDays(5);
        $invoicings = invoicing_pinjaman::where('period', $sekarangUdahDikurangi)
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
            // update va sesuai nominal tagihan
            $xendit = xenditApi::first();
            $url = $xendit->url . "/v2/payment_methods/" . $invoice->perusahaan->payment_method_id;

            $data = [
                "virtual_account" => [
                    "amount" => $invoice->perusahaan->pinjamanBerjalan->sum('total_angsuran_perbulan'),
                ],
                "description" => "updated fixed amount tagihan untuk jatuh tempo " .  $invoice->period
            ];

            $response = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withBasicAuth($xendit->secret, '')
                // ->withHeaders([])
                ->withBody(json_encode($data), 'application/json')
                ->patch($url);
            $link = url(env('APP_URL')) . '/sIztJyJPErLl52H1s1zlXu5q5z5k6mMwBYC6VQlGQ65eMNGSB5u/rLl52H1s1zl/' . $invoice->id . '/' . substr($invoice->perusahaan->id_code_perusahaan, 1);

            $data = [
                'subject' => 'Pemberitahuan Invoice',
                'title' => 'Pemberitahuan Invoice',
                'to' => $invoice->perusahaan->email_perusahaan,
                'link' => $link,
                'invoice' => $invoice
            ];
            Mail::to($data['to'])->send(new InvoiceEmail($data));
            // return view("admin.ksp.invoicing.index", compact('invoice'));
        }
    }

    public function invoicee($cekkk, $id, $code_perusahaan)
    {
        if ($cekkk == "rLl52H1s1zl") {
            $getPeriod = invoicing_pinjaman::where('id', $id)
                ->with('perusahaan')
                ->first();
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
    }
}
