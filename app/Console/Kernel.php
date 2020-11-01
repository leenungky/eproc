<?php

namespace App\Console;

use App\Console\Commands\RefreshTenderWorkflow;
use App\Console\Commands\ExpiryDocument;
use App\Jobs\SyncSapPRList;
use App\Jobs\SyncSapRefBank;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RefreshTenderWorkflow::class,
        ExpiryDocument::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('expiry:status')->dailyAt('11:30');
        $schedule->command('expiry:status')->dailyAt('22:00');
        $schedule->command('sanction:expiry')->dailyAt('12:00');
        $schedule->command('sanction:expiry')->dailyAt('22:30');
        $schedule->command('sanction:start')->dailyAt('12:30');
        $schedule->command('sanction:start')->dailyAt('23:00');
        $schedule->command('po:sap')->dailyAt('23:00');
        // $schedule->job(new SyncSapPRList)->everyFiveMinutes();
        // $schedule->job(new SyncSapPRList)->everyMinute();
        $schedule->job(new SyncSapPRList)->daily();
        $schedule->job(new SyncSapRefBank)->dailyAt('01:00'); //daily at midnight

        $schedule->command('queue:work --tries=5 --stop-when-empty')
            ->withoutOverlapping()
            ->everyMinute()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
