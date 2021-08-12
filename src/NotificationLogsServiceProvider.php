<?php

namespace Zenegal\NotificationLogs;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Zenegal\NotificationLogs\Commands\PruneOldMailables;
use Zenegal\NotificationLogs\Listeners\LogSendingMail;
use Zenegal\NotificationLogs\Listeners\LogSendingNotification;
use Zenegal\NotificationLogs\Listeners\LogSentMail;
use Zenegal\NotificationLogs\Listeners\LogSentNotification;

class NotificationLogsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['events']->listen(MessageSending::class, LogSendingMail::class);
        $this->app['events']->listen(MessageSent::class, LogSentMail::class);

        $this->app['events']->listen(NotificationSending::class, LogSendingNotification::class);
        $this->app['events']->listen(NotificationSent::class, LogSentNotification::class);

        $this->registerMailableData();

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PruneOldMailables::class,
            ]);
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('notification-logs:prune')->hourly();
        });

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notification-logs');
    }

    private function registerMailableData()
    {
        $existingCallback = Mailable::$viewDataCallback;

        Mailable::buildViewDataUsing(function ($mailable) use ($existingCallback) {
            $data = [];

            if ($existingCallback) {
                $data = call_user_func($existingCallback, $mailable);

                if (! is_array($data)) {
                    $data = [];
                }
            }

            $modal = null;
            $modalId = null;

            if (isset($mailable->viewData['order'])) {
                $reflection = new \ReflectionClass($mailable->viewData['order']);
                $modal = $reflection->getName();
                $modalId = $mailable->viewData['order']->id;
            }

            return array_merge($data, [
                '__notification_logs' => [
                    'uuid' => Str::uuid(),
                    'store_id' => config('store.id'),
                    'medium' => 'email',
                    'subject' => $mailable->subject,
                    'recipients' => collect($mailable->to)->pluck('address')->toArray(),
                    'mailable_name' => get_class($mailable),
                    'mailable' => serialize(clone $mailable),
                    'modal' => $modal,
                    'modal_id' => $modalId,
                    'is_queued' => in_array(ShouldQueue::class, class_implements($mailable)),
                ]
            ]);
        });
    }
}
