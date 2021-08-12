<?php

namespace Zenegal\NotificationLogs\Commands;

use Illuminate\Console\Command;
use Zenegal\NotificationLogs\Models\NotificationLog;

/**
 * Class PruneOldMailables
 * @package Zenegal\NotificationLogs\Commands
 */
class PruneOldMailables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification-logs:prune {--hours=72 : The number of hours to retain mails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete mailables from old sent notification logs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $prune = NotificationLog::where('is_sent', 1)
            ->whereNotNull('mailable')
            ->where('created_at', '<=', now()->subHours($this->option('hours')))
            ->update([
                'mailable' => null,
            ]);

        $this->info($prune.' mails pruned.');
    }
}
