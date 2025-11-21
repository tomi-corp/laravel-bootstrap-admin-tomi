<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceEmail;
use App\Mail\InvoiceKeLender;
use App\Mail\ReminderPinjamanPayroll;
use App\Models\disbursement;
use App\Models\download;
use App\Models\history_invoicing_lender;
use App\Models\invoicing_pinjaman;
use App\Models\lender;
use App\Models\lender_update;
use App\Models\ListPerusahaan;
use App\Models\pinjaman;
use App\Models\pinjaman_pending_notification;
use App\Models\rekening_pandu;
use App\Models\rekening_tomi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Flasher\Laravel\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\Public\Ekasbon\DaftarEkasbonRequest;
use Illuminate\Support\Facades\Http;


class DashboardController extends Controller
{
    public function dashboard()
    {
        // register
        $register = User::select(['id', 'created_at']);
        $totalRegister = $register->count();
        $registerBulanIni = $register->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $registerBulanKemarin = $register->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        $download = download::select(['id', 'created_at']);
        $totalDownload = $download->count();
        $downloadBulanIni = $download->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $downloadBulanKemarin = $download->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();


        $registerRiwayat = User::orderBy('created_at', 'desc')->take(3)
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

        $merged = $registerRiwayat->merge($pinjaman);
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

        $persentaseRegister =  ($registerBulanIni - $registerBulanKemarin) * $registerBulanKemarin * 100 / 100;
        $persentaseUnduhan =  ($downloadBulanIni - $downloadBulanKemarin) * $downloadBulanKemarin * 100 / 100;

        $register = [
            'persentaseRegister' => $persentaseRegister,
            'registerBulanIni' => $registerBulanIni,
            'activiti' => $dataFinal,
            'totalRegister' => $totalRegister,
            'jumlahDownload' => $totalDownload,
            'downloadBulanIni' => $downloadBulanIni,
            'persentaseDonwnload' => $persentaseUnduhan,
        ];
        // transaction
        return view('admin.dashboard.index', compact('register'));
    }

    public function invoicee($encrypted, $unique, $idEnc, $codePerusahaanEnc)
    {
        $decrypted = Crypt::decryptString($encrypted);
        $uniqueDec = Crypt::decryptString($unique);
        $idDec = Crypt::decryptString($idEnc);
        $codeDec = Crypt::decryptString($codePerusahaanEnc);
        $finalCodeDec = '#' . $codeDec;

        if ($decrypted === 'penagihanInvoicingTOMI' && $uniqueDec === 'uniqueCodeDariBackendTomi') {
            $perusahaan = ListPerusahaan::where('id_code_perusahaan', $finalCodeDec)
                ->with('lender')
                ->first();
            $invoice = invoicing_pinjaman::where('id', $idDec)
                ->where('id_list_perusahaan', $perusahaan->id)
                ->first();


            if (!$invoice) {
                abort(404);
            }

            // $periodMonth = Carbon::parse($invoice->period)->month;
            $periodMonth = Carbon::parse($invoice->period)->format('m');
            $periodMonthT = Carbon::parse($invoice->period)->format('M');
            $invoice->load(['pinjaman' => function ($query) use ($periodMonth) {
                $query->whereHas('jatuh_tempoSesuaiInvoice', function ($q) use ($periodMonth) {
                    $q->whereMonth('jatuh_tempo', $periodMonth);
                    $q->with('lender');
                })->with('angsuran');
            }]);
            $pinjaman = $invoice->pinjaman;
            $noInv = "INV-" . $periodMonthT . '-' . $perusahaan->id_code_perusahaan . "-" . $invoice->id;
            // ada perbaikan invoicing nantiiii
            // $rekeningRepayment = rekening_tomi::with('bank')->first();
            $lender = Lender::where('id', $perusahaan->lender->id)->with('bank')->first();


            return view("admin.ksp.invoicing.index", compact('invoice', 'lender', 'pinjaman', 'noInv'));
        }
    }

    public function invoiceeLender($encrypted, $unique, $idEnc, $codeLenderEnc)
    {
        $decrypted = Crypt::decryptString($encrypted);
        $uniqueDec = Crypt::decryptString($unique);
        $idDec = Crypt::decryptString($idEnc);
        $codeDec = Crypt::decryptString($codeLenderEnc);
        $finalCodeDec = '#' . $codeDec;

        if ($decrypted === 'penagihanInvoicingKelender' && $uniqueDec === 'uniqueCodeDariBackendTomi') {
            $historyInvoicingLenderPending = history_invoicing_lender::where('id', $idDec)
                ->with([
                    'lender.pinjaman.disbursement',
                    'pendingApproval'
                ])->first();

            if ($historyInvoicingLenderPending && $historyInvoicingLenderPending->lender) {
                $filteredPinjaman = $historyInvoicingLenderPending->lender->pinjaman->filter(function ($pinjaman) use ($historyInvoicingLenderPending) {
                    return $pinjaman->disbursement &&
                        Carbon::parse($pinjaman->disbursement->created_at)->format('Y-m') === Carbon::parse($historyInvoicingLenderPending->period)->format('Y-m');
                });

                $historyInvoicingLenderPending->lender->total_pinjaman = $filteredPinjaman->count();
                $historyInvoicingLenderPending->lender->total_biaya_admin = $filteredPinjaman->sum('biaya_admin_pandu');
                $historyInvoicingLenderPending->lender->total_biaya_layanan = $filteredPinjaman->sum('biaya_layanan');
                $historyInvoicingLenderPending->lender->setRelation('pinjaman', $filteredPinjaman);
                $rekening = rekening_pandu::with('bank')->first();
                return view("admin.ksp.lender.invoicing.invoicing", compact('historyInvoicingLenderPending', 'rekening'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }


    public function registerEkasbon(DaftarEkasbonRequest $req)
    {
        $data = $req->validated();

        // Ambil token reCAPTCHA
        $recaptchaToken = $data['g-recaptcha-response'];

        // Verifikasi token
        if (!$this->verifyRecaptcha($recaptchaToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan gagal. Anda mungkin dianggap bot.',
            ], 400);
        }

        // 👉 LANJUTKAN SIMPAN DATA KE DATABASE DI SINI
        // Contoh:
        // Perusahaan::create([
        //     'nama' => $data['nama_perusahaan'],
        //     'jumlah_karyawan' => $data['jumlah_karyawan'],
        //     // dll...
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Pihak kami akan menghubungi Anda secepatnya.'
        ]);
    }

    protected function verifyRecaptcha(string $token): bool
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();
            // Verifikasi: success = true dan score >= 0.5 (opsional, tapi disarankan)
            return $result['success'] && ($result['score'] ?? 1.0) >= 0.5;
        } catch (\Exception $e) {
            // \Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
            return false;
        }
    }

    // public function test()
    // {
    //     $sekarang = Carbon::now()->format('Y-m-d');
    //     // $date = Carbon::now()->lastOfMonth()->endOfDay();
    //     $date = Carbon::parse('2025-10-1');

    //     $lenders = lender::withWhereHas('pinjaman', function ($q) use ($date) {
    //         $q->whereYear('created_at', $date)
    //             ->whereMonth('created_at', $date)
    //             ->whereIn('status', ['approve', 'done']);
    //     })->get();

    //     foreach ($lenders as $lender) {
    //         $cekAdaAtauTidak =  history_invoicing_lender::where('id_lender', $lender->id)
    //             ->where('period', $date->firstOfMonth()->format('Y-m-d'))->first();
    //         if (!isset($cekAdaAtauTidak)) {
    //             $historyInvocing = new history_invoicing_lender();
    //             $historyInvocing->id_lender = $lender->id;
    //             $historyInvocing->period = $date->firstOfMonth()->format('Y-m-d');
    //             $historyInvocing->status = 'belum_lunas';
    //             $historyInvocing->save();
    //             $historyInvocing->no_invoice = "INV-TOMI-" . $date->firstOfMonth()->format('M') . "-" . $lender->id_code_lender . "-" . $historyInvocing->id;
    //             $historyInvocing->save();
    //             $encrypted = Crypt::encryptString('penagihanInvoicingKelender');
    //             $unique = Crypt::encryptString('uniqueCodeDariBackendTomi');
    //             $idEnc = Crypt::encryptString($historyInvocing->id);
    //             $codeLenderEnc = Crypt::encryptString(substr($historyInvocing->lender->id_code_lender, 1));

    //             $link = url(env('APP_URL')) . '/invoicing-layanan' . '/' . $encrypted . '/' . $unique . '/' . $idEnc . '/' . $codeLenderEnc;
    //             // 'to' => $historyInvocing->lender->email_lender,
    //             $data = [
    //                 'title' => 'Pemberitahuan Invoice Biaya Layanan dan Fee',
    //                 'to' => 'imamrovid9@gmail.com',
    //                 'link' => $link,
    //                 'rekening' => $historyInvocing->lender
    //             ];

    //             try {
    //                 Mail::to($data['to'])->send(new InvoiceKeLender($data));
    //             } catch (\Throwable $th) {
    //                 dd($th);
    //                 Log::channel('cronjob')->info($sekarang . ' Error : ' . $th);
    //             }
    //         }
    //     }

    //     Log::channel('cronjob')->info($date . ' Cronjob Create Invoice lender telah dijalankan');
    // }

    public function test() {}
}
