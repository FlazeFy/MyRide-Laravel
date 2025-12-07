<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Schedule\AuditSchedule;
use App\Schedule\WashSchedule;
use App\Schedule\CleanSchedule;

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
        // $schedule->call([new AuditSchedule, 'audit_apps'])->weeklyOn(1, '5:00');	
        // $schedule->call([new AuditSchedule, 'audit_weekly_stats'])->weeklyOn(1, '6:00');	
        // $schedule->call([new AuditSchedule, 'audit_yearly_stats'])->yearlyOn(1, 3, '01:15');		
        // $schedule->call([new AuditSchedule, 'audit_dashboard'])->weeklyOn(2, '1:50');
        // $schedule->call([new CleanSchedule, 'clean_history'])->dailyAt('01:00');
        // $schedule->call([new WashSchedule, 'wash_history'])->dailyAt('01:00');
        // $schedule->call([new WashSchedule, 'wash_reminder'])->dailyAt('01:30');
        // $schedule->call([new WashSchedule, 'wash_deleted_vehicle'])->dailyAt('02:00');

        // In development
        // $schedule->command(AuditSchedule::audit_error())->everyMinute();
        // $schedule->command(AuditSchedule::audit_apps())->everyMinute();
        // $schedule->command(AuditSchedule::audit_weekly_stats())->everyMinute();
        // $schedule->command(AuditSchedule::audit_yearly_stats())->everyMinute();
        // $schedule->command(AuditSchedule::audit_dashboard())->everyMinute();
        // $schedule->command(CleanSchedule::clean_history())->everyMinute();
        // $schedule->command(WashSchedule::wash_history())->everyMinute();
        // $schedule->command(WashSchedule::wash_reminder())->everyMinute();
        // $schedule->command(WashSchedule::wash_deleted_vehicle())->everyMinute();
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
