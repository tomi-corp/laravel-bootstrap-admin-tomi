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
use App\Models\jatuh_tempo;
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
use App\Models\daftar_e_kasbon;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;


class DashboardController extends Controller
{
    public function applicationHistory()
    {
        return view('admin.tomi-dana.application-history.index');
    }

    public function applicationHistoryTable()
    {
        $query = pinjaman::query()
            ->with(['data_karyawan', 'perusahaan'])
            ->select('pinjaman.*');

        return DataTables::eloquent($query)
            ->addColumn('tanggal', fn ($row) => optional($row->created_at)->format('d/m/Y H:i') ?? '-')
            ->addColumn('id_pengajuan', fn ($row) => $row->id_pinjaman ?? $row->id)
            ->addColumn('peminjam', fn ($row) => strtoupper($row->data_karyawan?->nama_karyawan ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('nominal', fn ($row) => 'Rp. ' . number_format((float) ($row->nominal ?? 0), 0, ',', '.'))
            ->addColumn('otp', fn ($row) => strtoupper($row->status_otp ?? '-'))
            ->addColumn('ttd', fn ($row) => strtoupper($row->ttd ?? '-'))
            ->addColumn('status_label', fn ($row) => strtoupper(str_replace('_', ' ', $row->status ?? '-')))
            ->toJson();
    }

    public function dashboard()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfPreviousMonth = $startOfMonth->copy()->subMonth();
        $endOfPreviousMonth = $startOfMonth->copy()->subSecond();

        $percentageChange = static function (int|float $current, int|float $previous): int {
            if ($previous == 0) {
                return $current > 0 ? 100 : 0;
            }

            return (int) round((($current - $previous) / $previous) * 100);
        };

        $totalPerusahaan = ListPerusahaan::query()->count();
        $totalPerusahaanBulanLalu = ListPerusahaan::query()
            ->where('created_at', '<=', $endOfPreviousMonth)
            ->count();
        $totalPengajuan = pinjaman::query()->count();
        $totalPengajuanBulanLalu = pinjaman::query()
            ->where('created_at', '<=', $endOfPreviousMonth)
            ->count();
        $totalTransaksiBulanIni = pinjaman::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        $totalTransaksiBulanLalu = pinjaman::query()
            ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->count();
        $tugasPerluTindakan = pinjaman::query()
            ->whereIn('status', ['pending', 'on_proses'])
            ->count();
        $tugasPerluTindakanBulanIni = pinjaman::query()
            ->whereIn('status', ['pending', 'on_proses'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        $tugasPerluTindakanBulanLalu = pinjaman::query()
            ->whereIn('status', ['pending', 'on_proses'])
            ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->count();
        $persentaseTotalPerusahaan = $percentageChange($totalPerusahaan, $totalPerusahaanBulanLalu);
        $persentaseTotalPengajuan = $percentageChange($totalPengajuan, $totalPengajuanBulanLalu);
        $persentaseTransaksiBulanIni = $percentageChange($totalTransaksiBulanIni, $totalTransaksiBulanLalu);
        $persentaseTugasPerluTindakan = $percentageChange($tugasPerluTindakanBulanIni, $tugasPerluTindakanBulanLalu);
        $menungguVerifikasi = pinjaman::query()
            ->where('status_otp', 'pending')
            ->count();
        $mendekatiJatuhTempo = jatuh_tempo::query()
            ->whereBetween('jatuh_tempo', [
                $now->copy()->startOfDay(),
                $now->copy()->addDays(3)->endOfDay(),
            ])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'lunas');
            })
            ->count();
        $dokumenPerluDilihat = pinjaman::query()
            ->where('document_privy', 'n')
            ->count();
        $totalPengajuanTomiDana = pinjaman::query()->count();
        $totalPengajuanTomiGaji = DB::table('salary_financings')->count();
        $pendingTomiGaji = DB::table('salary_financings')
            ->where('status', 'pending')
            ->count();
        $disetujuiTomiGaji = DB::table('salary_financings')
            ->where('status', 'approve')
            ->count();
        $totalPengajuanTomiInvoice = DB::table('financing_request_invoices')
            ->whereNull('deleted_at')
            ->count();
        $pendingTomiInvoice = DB::table('financing_request_invoices')
            ->whereNull('deleted_at')
            ->where('status', 'in_progress')
            ->count();
        $disetujuiTomiInvoice = DB::table('financing_request_invoices')
            ->whereNull('deleted_at')
            ->where('status', 'approve')
            ->count();
        $pengajuanBaruHariIni = pinjaman::query()
            ->whereBetween('created_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()])
            ->count();
        $verifikasiSelesai = pinjaman::query()
            ->where('status_otp', 'success')
            ->where('ttd', 'y')
            ->count();
        $pembayaranBerhasilHariIni = pinjaman::query()
            ->where('status', 'approve')
            ->whereBetween('created_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()])
            ->count();
        $mouAktifBulanIni = ListPerusahaan::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        $pendingTomiDana = pinjaman::query()
            ->whereIn('status', ['pending', 'on_proses'])
            ->count();
        $disetujuiTomiDana = pinjaman::query()
            ->where('status', 'approve')
            ->count();

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


        $activityRows = collect();

        User::orderByDesc('created_at')
            ->take(5)
            ->get()
            ->each(function ($item) use ($activityRows) {
                $activityRows->push([
                    'created_at' => $item->created_at,
                    'name' => $item->name,
                    'activity' => 'User baru mendaftar',
                    'product' => '-',
                    'status' => 'Baru',
                ]);
            });

        pinjaman::orderByDesc('created_at')
            ->take(5)
            ->with('data_karyawan')
            ->get()
            ->each(function ($item) use ($activityRows) {
                $activityRows->push([
                    'created_at' => $item->created_at,
                    'name' => optional($item->data_karyawan)->nama_karyawan ?? '-',
                    'activity' => 'Peminjam baru mengajukan',
                    'product' => 'TomiDana',
                    'status' => ucfirst(str_replace('_', ' ', (string) ($item->status ?? 'pending'))),
                ]);
            });

        DB::table('salary_financings')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->each(function ($item) use ($activityRows) {
                $activityRows->push([
                    'created_at' => $item->created_at,
                    'name' => $item->nama_perusahaan ?? $item->company_name ?? $item->nama ?? '-',
                    'activity' => 'Pengajuan baru masuk',
                    'product' => 'TomiGaji',
                    'status' => ucfirst(str_replace('_', ' ', (string) ($item->status ?? 'pending'))),
                ]);
            });

        DB::table('financing_request_invoices')
            ->leftJoin('clients', 'clients.id', '=', 'financing_request_invoices.client_id')
            ->whereNull('financing_request_invoices.deleted_at')
            ->orderByDesc('financing_request_invoices.created_at')
            ->take(5)
            ->get([
                'financing_request_invoices.created_at',
                'financing_request_invoices.status',
                'clients.name as client_name',
            ])
            ->each(function ($item) use ($activityRows) {
                $activityRows->push([
                    'created_at' => $item->created_at,
                    'name' => $item->client_name ?? '-',
                    'activity' => 'Pengajuan baru masuk',
                    'product' => 'TomiInvoice',
                    'status' => ucfirst(str_replace('_', ' ', (string) ($item->status ?? 'pending'))),
                ]);
            });

        $dataFinal = $activityRows
            ->sortByDesc(fn ($item) => Carbon::parse($item['created_at']))
            ->take(5)
            ->map(function ($item) {
                $item['created_at'] = Carbon::parse($item['created_at'])->diffForHumans();
                return $item;
            })
            ->values()
            ->all();

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

        return view('admin.dashboard.index', compact(
            'register',
            'totalPerusahaan',
            'persentaseTotalPerusahaan',
            'totalPengajuan',
            'persentaseTotalPengajuan',
            'totalTransaksiBulanIni',
            'persentaseTransaksiBulanIni',
            'tugasPerluTindakan',
            'persentaseTugasPerluTindakan',
            'menungguVerifikasi',
            'mendekatiJatuhTempo',
            'dokumenPerluDilihat',
            'totalPengajuanTomiDana',
            'totalPengajuanTomiGaji',
            'pendingTomiGaji',
            'disetujuiTomiGaji',
            'totalPengajuanTomiInvoice',
            'pendingTomiInvoice',
            'disetujuiTomiInvoice',
            'pengajuanBaruHariIni',
            'verifikasiSelesai',
            'pembayaranBerhasilHariIni',
            'mouAktifBulanIni',
            'pendingTomiDana',
            'disetujuiTomiDana'
        ));
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

        $recaptchaToken = $data['g-recaptcha-response'];
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('SECRET_G_CAPTCHA'),
            'response' => $recaptchaToken,
            'remoteip' => request()->ip(),
        ]);

        if (!$response['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi keamanan gagal. Anda mungkin dianggap bot.',
            ], 400);
        }

        $daftar = new daftar_e_kasbon();
        $daftar->nama_perusahaan = $req->namaPerusahaan;
        $daftar->jumlahKaryawan = $req->jumlahKaryawan;
        $daftar->pic = $req->pic;
        $daftar->whatsapp = $req->whatsapp;
        $daftar->email = $req->email;
        $daftar->save();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Pihak kami akan menghubungi Anda secepatnya.'
        ]);
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
