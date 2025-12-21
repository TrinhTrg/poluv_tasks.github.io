<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the task due notification scanner
Schedule::command('todo:scan-due')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule server metrics monitoring (every 5 minutes)
Schedule::command('monitor:server-metrics')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();

// Schedule incomplete tasks reminder (daily at midnight)
Schedule::command('tasks:send-incomplete-reminders')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer();
