<?php

namespace App\Console\Commands;

use App\Models\kyc_user;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpiredKycCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expired-kyc-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus kyc yang lebih dari 180 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        kyc_user::where('created_at', '<', Carbon::now()->subDays(180))->delete();
    }
}
