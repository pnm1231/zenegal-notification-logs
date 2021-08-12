<?php

namespace Zenegal\NotificationLogs\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Zenegal\NotificationLogs\Models\NotificationLog;

class LogSentMail
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
     * @param MessageSent $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        if (! isset($event->data['__notification_logs'])) {
            return;
        }

        // If recipients is empty, It means mailable is part of a notification
        // which will be handled by the notification listener
        if (isset($event->data['__notification_logs']['uuid']) && count($event->data['__notification_logs']['recipients']) > 0) {
            $key = isset($event->data['__notification_logs']['id']) ? 'id' : 'uuid';

            NotificationLog::where($key, $event->data['__notification_logs'][$key])->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
        }
    }
}
