<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use App\Models\Affiliate;
use App\Models\Entertainer;

class Transaction extends Model
{
    private const EXCLUDE_ARCHIVED_SCOPE = 'exclude_archived_transactions';

    public const STATUS_CANCELED = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_REFUNDED = 2;

    private static ?bool $hasArchivedAtColumn = null;

    public const COMMISSION_STATUS_PENDING = 'pending';
    public const COMMISSION_STATUS_APPROVED = 'approved';
    public const COMMISSION_STATUS_PAID = 'paid';
    public const COMMISSION_STATUS_REVERSED = 'reversed';

    protected $casts = [
        'cart_items' => 'array',
        'checked_in_status' => 'boolean',
        'shipping_same_as_billing' => 'boolean',
        'package_use_date' => 'date',
        'checked_in_at_pacific' => 'datetime',
        'affiliate_commission_hold_until' => 'datetime',
        'affiliate_commission_approved_at' => 'datetime',
        'affiliate_commission_reversed_at' => 'datetime',
        'entertainer_commission_hold_until' => 'datetime',
        'entertainer_commission_approved_at' => 'datetime',
        'entertainer_commission_reversed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(self::EXCLUDE_ARCHIVED_SCOPE, function (Builder $builder) {
            if (!self::supportsArchiving()) {
                return;
            }

            $builder->whereNull($builder->getModel()->getTable() . '.archived_at');
        });
    }

    private static function supportsArchiving(): bool
    {
        if (self::$hasArchivedAtColumn !== null) {
            return self::$hasArchivedAtColumn;
        }

        self::$hasArchivedAtColumn = Schema::hasColumn('transactions', 'archived_at');

        return self::$hasArchivedAtColumn;
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function getPackageTableLabelAttribute(): string
    {
        $cartItems = is_array($this->cart_items) ? $this->cart_items : [];

        $topLevelPackageIds = $this->normalizeMultiValuePackageField($cartItems['package_ids'] ?? null);
        if (count($topLevelPackageIds) > 1) {
            return 'Multiple';
        }

        $topLevelPackageNames = $this->normalizeMultiValuePackageField($cartItems['package_names'] ?? null);
        if (count($topLevelPackageNames) > 1) {
            return 'Multiple';
        }

        $packageEntries = collect($cartItems)
            ->filter(function ($item) {
                if (!is_array($item)) {
                    return false;
                }

                return isset($item['package_id']) || isset($item['package_name']);
            })
            ->values();

        if ($packageEntries->count() > 1) {
            return 'Multiple';
        }

        if ($packageEntries->count() === 1) {
            $first = $packageEntries->first();
            $name = trim((string) ($first['package_name'] ?? ''));

            $explodedNames = $this->normalizeMultiValuePackageField($name);
            if (count($explodedNames) > 1) {
                return 'Multiple';
            }

            $explodedPackageIds = $this->normalizeMultiValuePackageField($first['package_id'] ?? null);
            if (count($explodedPackageIds) > 1) {
                return 'Multiple';
            }

            if ($name !== '') {
                return html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if ($this->package) {
            return (string) $this->package->name;
        }

        return 'N/A';
    }

    public function getTransportModeLabelAttribute(): ?string
    {
        if (! $this->requiresTransportation()) {
            return null;
        }

        return $this->hasTransportationPickupDetails() ? 'Transportation' : 'Self Drive';
    }

    private function requiresTransportation(): bool
    {
        $cartItems = is_array($this->cart_items) ? $this->cart_items : [];

        foreach ($cartItems as $item) {
            if (!is_array($item)) {
                continue;
            }

            if ($this->isTruthy($item['transportation'] ?? ($item['transport'] ?? false))) {
                return true;
            }
        }

        if ($this->package) {
            return $this->package->transportation == 1
                || $this->package->transportation === true
                || $this->package->transportation === '1';
        }

        return false;
    }

    private function hasTransportationPickupDetails(): bool
    {
        return trim((string) ($this->transportation_pickup_time ?? '')) !== ''
            || trim((string) ($this->transportation_address ?? '')) !== ''
            || trim((string) ($this->transportation_phone ?? '')) !== ''
            || trim((string) ($this->transportation_guest ?? '')) !== ''
            || trim((string) ($this->transportation_note ?? '')) !== '';
    }

    private function isTruthy($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    public function getHasMultiplePackagesAttribute(): bool
    {
        return $this->package_table_label === 'Multiple';
    }

    public function scopeWithArchived(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::EXCLUDE_ARCHIVED_SCOPE);
    }

    public function scopeOnlyArchived(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::EXCLUDE_ARCHIVED_SCOPE)
            ->whereNotNull('archived_at');
    }

    public function scopeFinanciallyReportable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    private function normalizeMultiValuePackageField($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => trim((string) $item))
                ->filter(fn ($item) => $item !== '')
                ->values()
                ->all();
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return [];
        }

        if (str_contains($stringValue, '|')) {
            return collect(explode('|', $stringValue))
                ->map(fn ($item) => trim((string) $item))
                ->filter(fn ($item) => $item !== '')
                ->values()
                ->all();
        }

        if (str_contains($stringValue, ',')) {
            return collect(explode(',', $stringValue))
                ->map(fn ($item) => trim((string) $item))
                ->filter(fn ($item) => $item !== '')
                ->values()
                ->all();
        }

        return [$stringValue];
    }
}
