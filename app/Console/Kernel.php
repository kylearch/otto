<?php

namespace App\Console;

use App\Console\Commands\ScanForNewDocumentsCommand;
use App\Jobs\DailySnapshotJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [//
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Storage::put('kernel.txt', now()->format('Y-m-d H:i'));
        })->everyMinute();

        $schedule->command(ScanForNewDocumentsCommand::class)->everyFiveMinutes()->withoutOverlapping();

        $schedule->job(DailySnapshotJob::class)->dailyAt('0:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
