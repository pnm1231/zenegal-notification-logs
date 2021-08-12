<?php

namespace Zenegal\NotificationLogs\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Queue\InteractsWithQueue;
use Zenegal\NotificationLogs\Models\NotificationLog;

class LogSendingNotification
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
     * @param NotificationSending $event
     * @return void
     */
    public function handle(NotificationSending $event)
    {
        $mail = NotificationLog::whereUuid($event->notification->id)->first();

        if ($mail) {
            $mail->update([
                'is_sent' => false,
                'tries' => $mail->tries + 1
            ]);
        }
        else {
            $recipients = [];

            if ($event->notifiable instanceof AnonymousNotifiable) {
                if (is_array($event->notifiable->routes)) {
                    $recipients = array_values($event->notifiable->routes);
                }
            } else {
                $recipients = [$event->notifiable->routeNotificationFor('mail')];
            }

            NotificationLog::create([
                'uuid' => $event->notification->id,
                'store_id' => config('store.id'),
                'medium' => 'email',
                'subject'  =>$event->notification->toMail($event->notifiable)->subject,
                'recipients' => $recipients,
                'mailable_name' => get_class($event->notification),
                'mailable' => serialize(clone $event->notification),
                'is_queued' => in_array(ShouldQueue::class, class_implements($event->notification)),
                'is_notification' => true,
                'notifiable' => serialize(clone $event->notifiable),
                'is_sent' => false,
                'tries' => 1
            ]);
        }
    }
}
