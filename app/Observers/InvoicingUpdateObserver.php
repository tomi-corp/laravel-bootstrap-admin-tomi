<?php

namespace App\Observers;

use App\Models\history_invoicing_lender;
use App\Models\user_activity_logs;

class InvoicingUpdateObserver
{
    /**
     * Handle the history_invoicing_lender "created" event.
     */
    public function created(history_invoicing_lender $history_invoicing_lender): void
    {
        //
    }

    /**
     * Handle the history_invoicing_lender "updated" event.
     */
    public function updated(history_invoicing_lender $history_invoicing_lender): void
    {
        $activityCreate = new user_activity_logs();
        $activityCreate->admin_id = $history_invoicing_lender->update_by !== null ? $history_invoicing_lender->update_by : null;
        $activityCreate->activity_type = 'update';
        $activityCreate->description = "Updated invoicing with ID: {$history_invoicing_lender->id}";
        $activityCreate->ip_address = request()->ip();
        $activityCreate->user_agent = request()->userAgent();
        $activityCreate->save();
    }

    /**
     * Handle the history_invoicing_lender "deleted" event.
     */
    public function deleted(history_invoicing_lender $history_invoicing_lender): void
    {
        //
    }

    /**
     * Handle the history_invoicing_lender "restored" event.
     */
    public function restored(history_invoicing_lender $history_invoicing_lender): void
    {
        //
    }

    /**
     * Handle the history_invoicing_lender "force deleted" event.
     */
    public function forceDeleted(history_invoicing_lender $history_invoicing_lender): void
    {
        //
    }
}
