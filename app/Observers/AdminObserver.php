<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\user_activity_logs;

class AdminObserver
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Admin $admin): void
    {
        $activityCreate = new user_activity_logs();
        $activityCreate->admin_id = auth('admin')->user()->id;
        $activityCreate->activity_type = 'create';
        $activityCreate->description = "Created a new admin with ID: {$admin->id}";
        $activityCreate->ip_address = request()->ip();
        $activityCreate->user_agent = request()->userAgent();
        $activityCreate->save();
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Admin $admin): void
    {
        //
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Admin $admin): void
    {
        //
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Admin $admin): void
    {
        //
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Admin $admin): void
    {
        //
    }
}
