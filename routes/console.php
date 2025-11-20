<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})
    ->purpose('Display an inspiring quote')
    ->hourly();

Schedule::command('invoice:send')
    ->daily()
    ->at('23:59');

Schedule::command('lender:invoicing')
    ->monthlyOn(1, '00:01'); // Jalan tiap tanggal 1 jam 00:01 pagi

Schedule::command('app:reminder-pinjaman-payroll-cron')
    ->daily()
    ->at('00:01');

Schedule::command('reminderPinjamanYangPending:cron')
    ->everyTwoMinutes();

Schedule::command('hapuspinjaman:cron')
    ->everyTwoMinutes();

Schedule::command('app:expired-kyc-cron')
    ->daily();
