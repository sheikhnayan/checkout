<?php

namespace App\Helpers;

use App\Models\Package;
use App\Models\Transaction;
use Carbon\Carbon;

class PackageLimitHelper
{
    /**
     * Check if a package has available capacity for the current day
     */
    public static function canPurchase(Package $package, int $quantity = 1): array
    {
        $today = Carbon::today();
        
        if ($package->package_type === 'table') {
            return self::checkTableLimit($package, $quantity, $today);
        } else {
            return self::checkTicketLimit($package, $quantity, $today);
        }
    }

    private static function checkTicketLimit(Package $package, int $quantity, Carbon $today): array
    {
        if (!$package->daily_ticket_limit) {
            return ['allowed' => true, 'message' => '' ];
        }

        $soldToday = Transaction::where('type', $package->id)
            ->whereDate('created_at', $today)
            ->sum('package_number_of_guest');

        $availableTickets = $package->daily_ticket_limit - $soldToday;

        if ($availableTickets < $quantity) {
            return [
                'allowed' => false,
                'message' => "Only {$availableTickets} tickets remaining for today. Requested: {$quantity}"
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    private static function checkTableLimit(Package $package, int $tableQuantity, Carbon $today): array
    {
        if (!$package->daily_table_limit || !$package->guests_per_table) {
            return ['allowed' => true, 'message' => ''];
        }

        $bookedToday = Transaction::where('type', $package->id)
            ->whereDate('created_at', $today)
            ->count();

        $availableTables = $package->daily_table_limit - $bookedToday;

        if ($availableTables < 1) {
            return [
                'allowed' => false,
                'message' => "No tables available for today."
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Get available capacity for a package on a given day
     */
    public static function getAvailableCapacity(Package $package, ?Carbon $date = null): int
    {
        $date = $date ?? Carbon::today();

        if ($package->package_type === 'table') {
            if (!$package->daily_table_limit) {
                return PHP_INT_MAX;
            }

            $bookedToday = Transaction::where('type', $package->id)
                ->whereDate('created_at', $date)
                ->count();

            return max(0, $package->daily_table_limit - $bookedToday);
        } else {
            if (!$package->daily_ticket_limit) {
                return PHP_INT_MAX;
            }

            $soldToday = Transaction::where('type', $package->id)
                ->whereDate('created_at', $date)
                ->sum('package_number_of_guest');

            return max(0, $package->daily_ticket_limit - $soldToday);
        }
    }
}
