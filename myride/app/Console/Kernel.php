<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Schedule\AuditSchedule;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // In staging
        // $schedule->call([new AuditSchedule, 'audit_error'])->weeklyOn(1, '3:00');

        // In development
        // $schedule->command(AuditSchedule::audit_error())->everyMinute();
        // $schedule->command('dusk:run')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
