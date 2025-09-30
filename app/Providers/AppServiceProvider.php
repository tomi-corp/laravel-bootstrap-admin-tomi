<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\history_invoicing_lender;
use App\Models\lender;
use App\Observers\AdminObserver;
use App\Observers\InvoicingUpdateObserver;
use App\Observers\LenderObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (env(key: 'APP_ENV') === 'local' && request()->server(key: 'HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme(scheme: 'https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        lender::observe(LenderObserver::class);
        Admin::observe(AdminObserver::class);
        history_invoicing_lender::observe(InvoicingUpdateObserver::class);
    }
}
