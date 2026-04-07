<?php

namespace App\Console;

use App\Console\Commands\ParseRawEmailsCommand;
use App\Console\Commands\PollImapCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        PollImapCommand::class,
        ParseRawEmailsCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('backup-monitor:poll-imap')->everyFiveMinutes();
        $schedule->command('backup-monitor:parse-raw-emails')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
