<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('commissions:approve-matured', function () {
    $controller = app(TransactionController::class);
    $result = $controller->approveMaturedCommissions();

    $this->info('Matured commission approval completed.');
    $this->line('Transactions scanned: ' . ($result['transactions_scanned'] ?? 0));
    $this->line('Promoter commissions approved: ' . ($result['affiliate_approved'] ?? 0));
    $this->line('Entertainer commissions approved: ' . ($result['entertainer_approved'] ?? 0));
})->purpose('Approve held promoter/entertainer commissions after hold period');
