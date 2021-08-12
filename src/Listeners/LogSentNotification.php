<?php

namespace Zenegal\NotificationLogs\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Zenegal\NotificationLogs\Models\NotificationLog;

class LogSentNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        NotificationLog::whereUuid($event->notification->id)->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }
}
