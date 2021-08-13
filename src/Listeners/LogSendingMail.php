<?php

namespace Zenegal\NotificationLogs\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Queue\InteractsWithQueue;
use Zenegal\NotificationLogs\Models\NotificationLog;

class LogSendingMail
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
     * @param MessageSending $event
     * @return void
     */
    public function handle(MessageSending $event)
    {
        if (! isset($event->data['__notification_logs'])) {
            return;
        }

        // If recipients is empty, It means mail is being sent using notification.
        // We ignore this mail because it will be handled by the notification listener.
        if (isset($event->data['__notification_logs']['uuid']) && count($event->data['__notification_logs']['recipients']) > 0) {
            // If the __notification_logs is set, It means the mail is a resent.
            // So We can increment the tries on the original mail without duplicating it.
            if (isset($event->data['__notification_logs']['id'])) {
                $mail = NotificationLog::find($event->data['__notification_logs']['id']);
            }

            if (isset($mail) && $mail) {
                $mail->update([
                    'is_sent' => false,
                    'tries' => $mail->tries + 1,
                ]);
            } else {
                // \Log::debug('LogSendingMail', $event->data['__notification_logs']);
                // return;
                NotificationLog::create([
                    'uuid' => $event->data['__notification_logs']['uuid'],
                    'store_id' => $event->data['__notification_logs']['store_id'],
                    'medium' => $event->data['__notification_logs']['medium'],
                    'subject'  => $event->data['__notification_logs']['subject'] ?? '',
                    'recipients' =>  $event->data['__notification_logs']['recipients'] ?? [],
                    'mailable_name' => $event->data['__notification_logs']['mailable_name'] ?? '',
                    'mailable' => $event->data['__notification_logs']['mailable'] ?? null,
                    'model' => $event->data['__notification_logs']['model'],
                    'model_id' => $event->data['__notification_logs']['model_id'],
                    'is_queued' => $event->data['__notification_logs']['is_queued'] ?? false,
                    'is_sent' => false,
                    'tries' => 1,
                ]);
            }
        }

        /*if (! isset($event->data['notification_log'])) {
            return;
        }

        \Log::debug('LogSendingMessage', [$event->data['notification_log']]);

        $event->data['notification_log']->update([
            'status' => 'sending',
        ]);*/
    }
}
