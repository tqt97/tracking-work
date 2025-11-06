<?php

namespace App\Listeners;

use App\Events\LeaveApprovalCreated;
use App\Jobs\SendApprovalNotificationJob;

class SendApprovalNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LeaveApprovalCreated $event)
    {
        dispatch(new SendApprovalNotificationJob($event->approval));
    }
}
