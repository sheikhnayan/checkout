<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('commissions:approve-matured', function () {
    $controller = app(TransactionController::class);
    $result = $controller->approveMaturedCommissions();

    $this->info('Matured commission approval completed.');
    $this->line('Transactions scanned: ' . ($result['transactions_scanned'] ?? 0));
    $this->line('affiliate commissions approved: ' . ($result['affiliate_approved'] ?? 0));
    $this->line('Entertainer commissions approved: ' . ($result['entertainer_approved'] ?? 0));
})->purpose('Approve held affiliate/entertainer commissions after hold period');

Artisan::command('reports:dispatch-automation', function () {
    $controller = app(ReportController::class);
    $result = $controller->dispatchDueAutomationSchedules();

    $this->info('Automation report dispatch completed.');
    $this->line('Schedules processed: ' . ($result['processed'] ?? 0));
    $this->line('Sent: ' . ($result['sent'] ?? 0));
    $this->line('Failed: ' . ($result['failed'] ?? 0));
})->purpose('Send due automation executive report schedules');

Schedule::command('reports:dispatch-automation')
    ->everyMinute()
    ->withoutOverlapping();
