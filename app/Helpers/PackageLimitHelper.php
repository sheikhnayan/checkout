<?php

namespace App\Helpers;

use App\Models\Package;
use App\Models\Transaction;
use Carbon\Carbon;

class PackageLimitHelper
{
    /**
     * Check if a package has available capacity for a target day.
     */
    public static function canPurchase(Package $package, int $quantity = 1, ?Carbon $date = null): array
    {
        $targetDate = $date ? $date->copy()->startOfDay() : Carbon::today();
        $requestedQuantity = max(1, (int) $quantity);
        
        if ($package->package_type === 'table') {
            return self::checkTableLimit($package, $requestedQuantity, $targetDate);
        } else {
            return self::checkTicketLimit($package, $requestedQuantity, $targetDate);
        }
    }

    private static function checkTicketLimit(Package $package, int $quantity, Carbon $targetDate): array
    {
        if (!$package->daily_ticket_limit) {
            return ['allowed' => true, 'message' => '' ];
        }

        $soldToday = self::countSoldUnitsForDate($package->id, $targetDate);

        $availableTickets = $package->daily_ticket_limit - $soldToday;

        if ($availableTickets < $quantity) {
            return [
                'allowed' => false,
                'message' => "Only {$availableTickets} ticket(s) are available for " . $targetDate->format('Y-m-d') . ". You requested {$quantity}."
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    private static function checkTableLimit(Package $package, int $requestedGuests, Carbon $targetDate): array
    {
        $guestsPerTable = max(0, (int) ($package->guests_per_table ?? 0));
        if ($guestsPerTable > 0 && $requestedGuests > $guestsPerTable) {
            return [
                'allowed' => false,
                'message' => "This table package allows up to {$guestsPerTable} guest(s) per booking."
            ];
        }

        $dailyTableLimit = max(0, (int) ($package->daily_table_limit ?? 0));
        if ($dailyTableLimit <= 0 || $guestsPerTable <= 0) {
            return ['allowed' => true, 'message' => ''];
        }

        $dailySeatLimit = $dailyTableLimit * $guestsPerTable;
        $bookedToday = self::countSoldUnitsForDate($package->id, $targetDate, true);
        $availableSeats = max($dailySeatLimit - $bookedToday, 0);

        if ($availableSeats < $requestedGuests) {
            return [
                'allowed' => false,
                'message' => "Only {$availableSeats} seat(s) are available for " . $targetDate->format('Y-m-d') . ". You requested {$requestedGuests}."
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Get available capacity for a package on a given day
     */
    public static function getAvailableCapacity(Package $package, ?Carbon $date = null): int
    {
        $date = ($date ?? Carbon::today())->copy()->startOfDay();

        if ($package->package_type === 'table') {
            $dailyTableLimit = max(0, (int) ($package->daily_table_limit ?? 0));
            $guestsPerTable = max(0, (int) ($package->guests_per_table ?? 0));

            if ($dailyTableLimit <= 0 || $guestsPerTable <= 0) {
                return PHP_INT_MAX;
            }

            $dailySeatLimit = $dailyTableLimit * $guestsPerTable;
            $soldSeats = self::countSoldUnitsForDate($package->id, $date, true);

            return max(0, $dailySeatLimit - $soldSeats);
        } else {
            $soldUnits = self::countSoldUnitsForDate($package->id, $date, false);
            if (!$package->daily_ticket_limit) {
                return PHP_INT_MAX;
            }

            return max(0, $package->daily_ticket_limit - $soldUnits);
        }
    }

    private static function countSoldUnitsForDate(int $packageId, Carbon $targetDate, bool $countGuestsAlways = false): int
    {
        $dateString = $targetDate->toDateString();

        $transactions = Transaction::query()
            ->where('status', 1)
            ->where(function ($query) use ($dateString) {
                $query->whereDate('package_use_date', $dateString)
                    ->orWhere(function ($fallbackQuery) use ($dateString) {
                        $fallbackQuery->whereNull('package_use_date')
                            ->whereDate('created_at', $dateString);
                    });
            })
            ->get(['package_id', 'package_number_of_guest', 'cart_items']);

        $soldUnits = 0;

        foreach ($transactions as $transaction) {
            $soldUnits += self::extractPackageUnitsFromTransaction($transaction, $packageId, $countGuestsAlways);
        }

        return max(0, (int) $soldUnits);
    }

    private static function extractPackageUnitsFromTransaction(Transaction $transaction, int $packageId, bool $countGuestsAlways = false): int
    {
        $units = 0;
        $cartItems = self::decodeCartItems($transaction->cart_items);

        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $itemPackageId = (int) ($item['package_id'] ?? $item['packageId'] ?? $item['pkgId'] ?? 0);
                if ($itemPackageId !== $packageId) {
                    continue;
                }

                $guests = max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1));
                $isMultiple = self::isTruthy($item['is_multiple'] ?? $item['isMultiple'] ?? true);
                $units += $countGuestsAlways ? $guests : ($isMultiple ? $guests : 1);
            }

            return $units;
        }

        if ((int) $transaction->package_id === $packageId) {
            $storedGuests = max(1, (int) ($transaction->package_number_of_guest ?? 1));
            return $countGuestsAlways ? $storedGuests : $storedGuests;
        }

        return 0;
    }

    private static function decodeCartItems($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    private static function isTruthy($value): bool
    {
        return $value === true || $value === 1 || $value === '1' || $value === 'true';
    }
}
