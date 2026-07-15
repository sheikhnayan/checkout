<?php

namespace App\Support;

use App\Models\Website;
use DateTimeZone;

class WebsiteTimezone
{
    public const DEFAULT = 'America/Los_Angeles';

    private const PREFERRED = [
        'America/Los_Angeles',
        'America/Denver',
        'America/Chicago',
        'America/New_York',
        'America/Phoenix',
        'Pacific/Honolulu',
        'America/Anchorage',
        'Europe/London',
    ];

    public static function normalize(?string $timezone): string
    {
        $timezone = trim((string) $timezone);

        if ($timezone === '' || !self::isValid($timezone)) {
            return self::DEFAULT;
        }

        return $timezone;
    }

    public static function isValid(?string $timezone): bool
    {
        if (!is_string($timezone) || trim($timezone) === '') {
            return false;
        }

        try {
            new DateTimeZone($timezone);

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public static function forWebsite($website): string
    {
        if ($website instanceof Website) {
            return self::normalize($website->timezone);
        }

        return self::DEFAULT;
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::PREFERRED as $identifier) {
            $options[$identifier] = self::label($identifier);
        }

        foreach (DateTimeZone::listIdentifiers() as $identifier) {
            if (isset($options[$identifier])) {
                continue;
            }

            $options[$identifier] = self::label($identifier);
        }

        return $options;
    }

    public static function label(string $timezone): string
    {
        $normalized = self::normalize($timezone);

        return match ($normalized) {
            'America/Los_Angeles' => 'Pacific Time - America/Los_Angeles',
            'America/Denver' => 'Mountain Time - America/Denver',
            'America/Chicago' => 'Central Time - America/Chicago',
            'America/New_York' => 'Eastern Time - America/New_York',
            'America/Phoenix' => 'Arizona Time - America/Phoenix',
            'Pacific/Honolulu' => 'Hawaii Time - Pacific/Honolulu',
            'America/Anchorage' => 'Alaska Time - America/Anchorage',
            default => str_replace('_', ' ', $normalized),
        };
    }
}