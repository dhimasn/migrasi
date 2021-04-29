<?php

namespace App\Console;

use App\Task\SignSendMailPenomoran;
use App\Task\SignSendMailSkIzinTel;
use App\Task\SignSendMailSkLoPenetapan;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $a = new SignSendMailSkIzinTel();
            $a->Process();
        })->everyMinute();

        $schedule->call(function () {
            $b = new SignSendMailSkLoPenetapan();
            $b->Process();
        })->everyMinute();

        $schedule->call(function () {
            $c = new SignSendMailPenomoran();
            $c->Process();
        })->everyMinute();
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
