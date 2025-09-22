<?php

namespace App\Observers;

use App\Models\Lender;
use App\Models\user_activity_logs;

class LenderObserver
{
    /**
     * Handle the Lender "created" event.
     */
    public function created(Lender $lender): void
    {
        // ngelog create
        $activityCreate = new user_activity_logs();
        $activityCreate->admin_id = auth('admin')->user()->id;
        $activityCreate->activity_type = 'create';
        $activityCreate->description = "Created a new lender with ID: {$lender->id}";
        $activityCreate->ip_address = request()->ip();
        $activityCreate->user_agent = request()->userAgent();
        $activityCreate->save();
    }

    /**
     * Handle the Lender "updated" event.
     */
    public function updated(Lender $lender): void
    {
        user_activity_logs::create([
            'activity_type' => 'update',
            'description' => "Updated lender with ID: {$lender->id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Lender "deleted" event.
     */
    public function deleted(Lender $lender): void
    {
        // ngelog delete
        $activityCreate = new user_activity_logs();
        $activityCreate->admin_id = auth('admin')->user()->id;
        $activityCreate->activity_type = 'delete';
        $activityCreate->description = "Deleted lender with ID: {$lender->id}";
        $activityCreate->ip_address = request()->ip();
        $activityCreate->user_agent = request()->userAgent();
        $activityCreate->save();
    }

    /**
     * Handle the Lender "restored" event.
     */
    public function restored(Lender $lender): void
    {
        //
    }

    /**
     * Handle the Lender "force deleted" event.
     */
    public function forceDeleted(Lender $lender): void
    {
        //
    }
}
