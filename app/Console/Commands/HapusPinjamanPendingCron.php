<?php

namespace App\Console\Commands;

use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HapusPinjamanPendingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hapuspinjaman:cron';

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
        $subday = Carbon::now()->subDays(1);
        $pinjamans = pinjaman::where('created_at', '<', $subday)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('status_otp', 'pending')
                    ->orWhere('ttd', 'n')
                    ->orWhere('document_privy', 'n');
            })
            ->with(['user', 'pinjaman_pending_notification'])
            ->get();
        $pinjamanIdsToDelete = [];
        foreach ($pinjamans as $pinjaman) {
            if (isset($pinjaman->pinjaman_pending_notification)) {
                sendNotification($pinjaman->user->token_firebase, "Pengajuan Dihapus 😔", "Karena tidak ada kelanjutan dalam 1x24 jam, permohonan pinjaman Anda telah kami hapus.");
                $pinjamanIdsToDelete[] = $pinjaman->id;
            }
        }

        if (!empty($pinjamanIdsToDelete)) {
            pinjaman::whereIn('id', $pinjamanIdsToDelete)->delete();
        }
    }
}
