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
    ->dailyAt('23:59:58')
    ->when(function () {
        return now()->isLastOfMonth();
    });
