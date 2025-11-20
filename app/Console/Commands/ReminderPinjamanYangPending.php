<?php

namespace App\Console\Commands;

use App\Models\pinjaman;
use App\Models\pinjaman_pending_notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReminderPinjamanYangPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminderPinjamanYangPending:cron';

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
        $batasWaktu = Carbon::now()->subMinutes(30);
        $pinjamans = pinjaman::where('status', 'pending')
            ->where('created_at', '<', $batasWaktu)
            ->where(function ($query) {
                $query->where('status_otp', 'pending')
                    ->orWhere('ttd', 'n')
                    ->orWhere('document_privy', 'n');
            })
            ->with(['user', 'pinjaman_pending_notification'])
            ->get();

        foreach ($pinjamans as $pinjaman) {
            if (!isset($pinjaman->pinjaman_pending_notification)) {
                sendNotification(
                    $pinjaman->user->token_firebase,
                    "Proses Pinjaman Belum Selesai! 🔔",
                    "Segera selesaikan proses pinjaman Anda! Mohon segera verifikasi OTP dan tinjau Dokumen PK pada history pinjaman e-Kasbon sebelum batas waktu 1x24 jam untuk menghindari pembatalan otomatis."
                );
                $notifReminderPinjaman = new pinjaman_pending_notification();
                $notifReminderPinjaman->pinjaman_id = $pinjaman->id;
                $notifReminderPinjaman->save();
            }
        }
    }
}
