<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Components\Magento\FetchContributors::class,
        \App\Console\Components\Magento\FetchPullRequests::class,
        \App\Console\Components\Magento\FetchRepositories::class,
        \App\Console\Components\Magento\FetchIssues::class,
        \App\Console\Components\Magento\GenerateStatistics::class,
        \App\Console\Components\Sitemap\Generate::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('magento:generate:statistics', ['--online'])->everyFifteenMinutes();
        $schedule->command('magento:generate:contributors')->everyThirtyMinutes();
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
