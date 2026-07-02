<?php

namespace App\Services;

use App\Models\AutomationReportSchedule;
use Carbon\Carbon;

class AutomationReportSchedulerService
{
    public function computeNextRunAt(AutomationReportSchedule $schedule, ?Carbon $from = null): ?Carbon
    {
        $timezone = $schedule->timezone ?: 'America/Los_Angeles';
        $base = ($from ?: now())->copy()->setTimezone($timezone)->seconds(0);

        [$hour, $minute, $second] = array_pad(explode(':', (string) $schedule->send_time), 3, '00');
        $targetHour = (int) $hour;
        $targetMinute = (int) $minute;
        $targetSecond = (int) $second;

        if (!empty($schedule->one_time_date)) {
            $oneTime = Carbon::parse($schedule->one_time_date, $timezone);
            if (!empty($schedule->one_time_time)) {
                [$otHour, $otMinute, $otSecond] = array_pad(explode(':', (string) $schedule->one_time_time), 3, '00');
                $oneTime->setTime((int) $otHour, (int) $otMinute, (int) $otSecond);
            } else {
                $oneTime->setTime($targetHour, $targetMinute, $targetSecond);
            }

            if ($oneTime->gt($base)) {
                return $oneTime->setTimezone(config('app.timezone'));
            }
        }

        if ($schedule->frequency === 'daily') {
            $candidate = $base->copy()->setTime($targetHour, $targetMinute, $targetSecond);
            if ($candidate->lte($base)) {
                $candidate->addDay();
            }

            return $candidate->setTimezone(config('app.timezone'));
        }

        if ($schedule->frequency === 'weekly') {
            $dayOfWeek = (int) ($schedule->weekly_day ?? 1);
            $candidate = $base->copy()->setTime($targetHour, $targetMinute, $targetSecond);
            while ((int) $candidate->dayOfWeek !== $dayOfWeek) {
                $candidate->addDay();
            }
            if ($candidate->lte($base)) {
                $candidate->addWeek();
            }

            return $candidate->setTimezone(config('app.timezone'));
        }

        if ($schedule->frequency === 'monthly') {
            $day = max(1, min(31, (int) ($schedule->monthly_day ?? 1)));
            $candidate = $base->copy()->startOfMonth()->day(min($day, $base->copy()->endOfMonth()->day))
                ->setTime($targetHour, $targetMinute, $targetSecond);

            if ($candidate->lte($base)) {
                $nextMonth = $base->copy()->addMonth()->startOfMonth();
                $candidate = $nextMonth->copy()->day(min($day, $nextMonth->copy()->endOfMonth()->day))
                    ->setTime($targetHour, $targetMinute, $targetSecond);
            }

            return $candidate->setTimezone(config('app.timezone'));
        }

        if ($schedule->frequency === 'yearly') {
            $month = max(1, min(12, (int) ($schedule->yearly_month ?? 1)));
            $day = max(1, min(31, (int) ($schedule->yearly_day ?? 1)));
            $candidate = Carbon::create(
                $base->year,
                $month,
                1,
                $targetHour,
                $targetMinute,
                $targetSecond,
                $timezone
            );
            $candidate->day(min($day, $candidate->copy()->endOfMonth()->day));

            if ($candidate->lte($base)) {
                $candidate->addYear();
                $candidate->day(min($day, $candidate->copy()->endOfMonth()->day));
            }

            return $candidate->setTimezone(config('app.timezone'));
        }

        if ($schedule->frequency === 'custom_month_range') {
            $candidate = $base->copy()->setTime($targetHour, $targetMinute, $targetSecond);
            if ($candidate->lte($base)) {
                $candidate->addMonth();
            }

            return $candidate->setTimezone(config('app.timezone'));
        }

        return null;
    }

    public function buildRangePayload(AutomationReportSchedule $schedule, Carbon $runAtAppTz): array
    {
        $tz = $schedule->timezone ?: 'America/Los_Angeles';
        $runAt = $runAtAppTz->copy()->setTimezone($tz);

        if ($schedule->frequency === 'daily') {
            $start = $runAt->copy()->subDay()->startOfDay();
            $end = $runAt->copy()->subDay()->endOfDay();

            return [
                'date_range' => 'custom',
                'custom_from' => $start->toDateString(),
                'custom_to' => $end->toDateString(),
                'timezone' => $tz,
            ];
        }

        if ($schedule->frequency === 'weekly') {
            $start = $runAt->copy()->subWeek()->startOfWeek(Carbon::SUNDAY);
            $end = $start->copy()->endOfWeek(Carbon::SATURDAY);

            return [
                'date_range' => 'custom',
                'custom_from' => $start->toDateString(),
                'custom_to' => $end->toDateString(),
                'timezone' => $tz,
            ];
        }

        if ($schedule->frequency === 'monthly') {
            $start = $runAt->copy()->subMonthNoOverflow()->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return [
                'date_range' => 'custom',
                'custom_from' => $start->toDateString(),
                'custom_to' => $end->toDateString(),
                'timezone' => $tz,
            ];
        }

        if ($schedule->frequency === 'yearly') {
            $start = $runAt->copy()->subYear()->startOfYear();
            $end = $start->copy()->endOfYear();

            return [
                'date_range' => 'custom',
                'custom_from' => $start->toDateString(),
                'custom_to' => $end->toDateString(),
                'timezone' => $tz,
            ];
        }

        if ($schedule->frequency === 'custom_month_range') {
            $from = $schedule->custom_from_month;
            $to = $schedule->custom_to_month;

            if ($from && $to) {
                return [
                    'date_range' => 'custom',
                    'custom_from' => $from->copy()->startOfMonth()->toDateString(),
                    'custom_to' => $to->copy()->endOfMonth()->toDateString(),
                    'timezone' => $tz,
                ];
            }
        }

        return [
            'date_range' => 'last_30_days',
            'timezone' => $tz,
        ];
    }
}
