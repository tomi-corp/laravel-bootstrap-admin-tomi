<?php

namespace App\Console\Commands;

use App\Models\history_invoicing_lender;
use App\Models\lender;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


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
            }
        }

        Log::channel('cronjob')->info($date . ' Cronjob Create Invoice lender telah dijalankan');
    }
}
