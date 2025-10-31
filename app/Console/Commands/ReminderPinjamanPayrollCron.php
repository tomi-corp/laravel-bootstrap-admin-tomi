<?php

namespace App\Console\Commands;

use App\Models\ListPerusahaan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderPinjamanPayroll;
use Illuminate\Support\Facades\Log;

class ReminderPinjamanPayrollCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-pinjaman-payroll-cron';

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
        $tanggalTarget = Carbon::now()->addDays(2);
        $perusahaan = ListPerusahaan::where('payment_date', $tanggalTarget->day)->with('pinjamanBerjalan')->get();
        foreach ($perusahaan as $perusahaanBerjalan) {
            foreach ($perusahaanBerjalan->pinjamanBerjalan as $berjalan) {
                $dataata = $berjalan->jatuh_tempoSesuaiInvoice->where('jatuh_tempo', $tanggalTarget->toDateString())->first();
                if ($dataata->status == "belum") {
                    $data = [
                        'subject' => 'Pengingat Jatuh Tempo Pembayaran',
                        'title' => 'Pengingat Jatuh Tempo Pembayaran',
                        'to' => $berjalan->user->email,
                        'nama_lengkap' =>  $berjalan->user->nama_lengkap,
                        'jatuh_tempo' =>  $tanggalTarget->toDateString(),
                        'no_pinjaman' =>  $berjalan->id_pinjaman,
                        'jumlah_tagihan' =>  $berjalan->total_angsuran_perbulan,
                        'angsuranKe' =>  $berjalan->jatuh_tempo->where('status', 'lunas')->count() + 1,
                        'totalAngsuran' =>  $berjalan->jatuh_tempo->count(),
                    ];
                    try {
                        Mail::to($data['to'])->send(new ReminderPinjamanPayroll($data));
                        sendNotification($berjalan->user->token_firebase, "Siap-siap Gajian! 💸", "Pemotongan angsuran pinjaman payroll Anda akan diproses otomatis 2 hari lagi, pada " . $tanggalTarget . ". Cek aplikasi/email untuk rincian.");
                    } catch (\Throwable $th) {
                        Log::channel('cronjob')->info($sekarang . ' Error : ' . $th);
                    }
                }
            }
        }
    }
}
