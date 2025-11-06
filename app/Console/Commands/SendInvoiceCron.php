<?php

namespace App\Console\Commands;

use App\Mail\InvoiceEmail;
use App\Models\invoicing_pinjaman;
use App\Models\ListPerusahaan;
use App\Models\rekening_tomi;
use App\Models\xenditApi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendInvoiceCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Penagihan ke PT
        // $rekeningTomi = rekening_tomi::with('bank')->first();

        $sekarang = Carbon::now()->format('Y-m-d');
        $hari_ini = Carbon::now()->day;
        $perusahaan = ListPerusahaan::whereRaw('payment_date - h_min_jatuh_tempo = ?', [$hari_ini])
            ->with(['lender' => function ($q) {
                $q->with('bank');
            }])
            ->get();

        $currentMonth = Carbon::now()->month;
        foreach ($perusahaan as $perusahaanYgsekarang) {
            $invoicings = invoicing_pinjaman::whereMonth('period', $currentMonth)
                ->where('status', 'belum_lunas')
                ->where('id_list_perusahaan', $perusahaanYgsekarang->id)
                ->with('perusahaan')
                ->get();

            foreach ($invoicings as $invoicing) {
                $getPeriod = invoicing_pinjaman::where('id', $invoicing->id)->first();
                $invoice = invoicing_pinjaman::where('id', $invoicing->id)
                    ->with(['perusahaan' =>  function ($q) use ($getPeriod) {
                        $q->with('lender');
                        $q->with(['pinjamanBerjalan' => function ($w) use ($getPeriod) {
                            $w->with(['jatuh_tempoSesuaiInvoice' =>  function ($e) use ($getPeriod) {
                                $e->whereMonth('jatuh_tempo', Carbon::parse($getPeriod->period)->format('m'));
                            }, 'data_karyawan']);
                        }]);
                    }])
                    ->first();

                $encrypted = Crypt::encryptString('penagihanInvoicingTOMI');
                $unique = Crypt::encryptString('uniqueCodeDariBackendTomi');
                $idEnc = Crypt::encryptString($invoice->id);
                $codePerusahaanEnc = Crypt::encryptString(substr($invoice->perusahaan->id_code_perusahaan, 1));

                $link = url(env('APP_URL')) . '/' . $encrypted . '/' . $unique . '/' . $idEnc . '/' . $codePerusahaanEnc;
                // 'to' => $invoice->perusahaan->email_perusahaan,

                $data = [
                    'title' => 'Pemberitahuan Invoice',
                    'to' => $invoice->perusahaan->email_perusahaan,
                    'link' => $link,
                    'invoice' => $invoice,
                    'rekening' => $perusahaanYgsekarang->lender
                ];

                try {
                    Mail::to($data['to'])->send(new InvoiceEmail($data));
                } catch (\Throwable $th) {
                    Log::channel('cronjob')->info($sekarang . ' Error : ' . $th);
                }
            }
        }


        Log::channel('cronjob')->info($sekarang . ' Cronjob Invoice perusahaan telah dijalankan');
    }
}
