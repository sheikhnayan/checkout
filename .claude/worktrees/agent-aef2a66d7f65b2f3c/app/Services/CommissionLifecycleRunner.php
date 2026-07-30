<?php

namespace App\Services;

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CommissionLifecycleRunner
{
    public function runSafely(): void
    {
        try {
            $cooldownSeconds = max((int) env('COMMISSION_RUN_COOLDOWN_SECONDS', 60), 10);
            $cooldownKey = 'commissions:auto-run:cooldown';
            $lockName = 'commissions:auto-run:lock';

            if (Cache::has($cooldownKey)) {
                return;
            }

            $lock = Cache::lock($lockName, 30);
            if (!$lock->get()) {
                return;
            }

            try {
                app(TransactionController::class)->approveMaturedCommissions();
                Cache::put($cooldownKey, 1, now()->addSeconds($cooldownSeconds));
            } catch (\Throwable $exception) {
                Log::warning('Commission auto-run failed during page request.', [
                    'message' => $exception->getMessage(),
                ]);
            } finally {
                try {
                    $lock->release();
                } catch (\Throwable $exception) {
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Commission auto-run lock bootstrap failed.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
