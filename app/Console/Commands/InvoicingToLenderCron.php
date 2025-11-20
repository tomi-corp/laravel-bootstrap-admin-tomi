<?php

namespace App\Console\Commands;

use App\Mail\InvoiceKeLender;
use App\Models\history_invoicing_lender;
use App\Models\lender;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoicingToLenderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lender:invoicing';

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
        $sekarang = Carbon::now()->format('Y-m-d');

        $date = Carbon::now()->lastOfMonth()->endOfDay();
        $lenders = lender::withWhereHas('pinjaman', function ($q) use ($date) {
            $q->whereYear('created_at', $date)
                ->whereMonth('created_at', $date)
                ->whereIn('status', ['approve', 'done']);
        })
            ->get();

        foreach ($lenders as $lender) {
            $cekAdaAtauTidak =  history_invoicing_lender::where('id_lender', $lender->id)
                ->where('period', $date->firstOfMonth()->format('Y-m-d'))->first();
            if (!isset($cekAdaAtauTidak)) {
                $historyInvocing = new history_invoicing_lender();
                $historyInvocing->id_lender = $lender->id;
                $historyInvocing->period = $date->firstOfMonth()->format('Y-m-d');
                $historyInvocing->status = 'belum_lunas';
                $historyInvocing->save();
                $historyInvocing->no_invoice = "INV-TOMI-" . $date->firstOfMonth()->format('M') . "-" . $lender->id_code_lender . "-" . $historyInvocing->id;
                $historyInvocing->save();

                $encrypted = Crypt::encryptString('penagihanInvoicingKelender');
                $unique = Crypt::encryptString('uniqueCodeDariBackendTomi');
                $idEnc = Crypt::encryptString($historyInvocing->id);
                $codeLenderEnc = Crypt::encryptString(substr($historyInvocing->lender->id_code_lender, 1));

                $link = url(env('APP_URL')) . '/invoicing-layanan/' . $encrypted . '/' . $unique . '/' . $idEnc . '/' . $codeLenderEnc;
                // 'to' => $historyInvocing->lender->email_lender,

                $data = [
                    'title' => 'Pemberitahuan Invoice Biaya Layanan dan Fee',
                    'to' => $historyInvocing->lender->email_lender,
                    'link' => $link,
                    'rekening' => $historyInvocing->lender
                ];

                try {
                    Mail::to($data['to'])->send(new InvoiceKeLender($data));
                } catch (\Throwable $th) {
                    Log::channel('cronjob')->info($sekarang . ' Error : ' . $th);
                }
            }
        }

        Log::channel('cronjob')->info($date . ' Cronjob Create Invoice lender telah dijalankan');
    }
}
