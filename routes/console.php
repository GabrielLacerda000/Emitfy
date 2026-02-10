<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('invoices:mark-overdue')
    ->daily()
    ->at('00:00');

Schedule::command('reminders:send')
    ->daily()
    ->at('09:00')
    ->withoutOverlapping()
    ->runInBackground();
