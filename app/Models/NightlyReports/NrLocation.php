<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\Website;
use App\Models\User;

class NrLocation extends Model
{
    protected $table = 'nr_locations';

    protected $fillable = [
        'website_id',
        'name',
        'legal_name',
        'short_name',
        'type',
        'address',
        'city',
        'state',
        'zip',
        'timezone',
        'phone',
        'dispatcher_phone',
        'gm_name',
        'gm_email',
        'club_inbox_email',
        'nightly_goal',
        'nightly_goals',
        'break_even',
        'historical_best',
        'active',
        'operating_days',
    ];

    protected $casts = [
        'active' => 'boolean',
        'nightly_goal' => 'decimal:2',
        'nightly_goals' => 'array',
        'break_even' => 'decimal:2',
        'historical_best' => 'decimal:2',
        'operating_days' => 'array',
    ];

    public function getNightlyGoalsAttribute()
    {
        if (array_key_exists('nightly_goals', $this->attributes) && !empty($this->attributes['nightly_goals'])) {
            $val = $this->attributes['nightly_goals'];
            return is_string($val) ? json_decode($val, true) : $val;
        }
        if (array_key_exists('operating_days', $this->attributes) && !empty($this->attributes['operating_days'])) {
            $val = $this->attributes['operating_days'];
            return is_string($val) ? json_decode($val, true) : $val;
        }
        return [];
    }

    public function getLegalNameAttribute()
    {
        if (array_key_exists('legal_name', $this->attributes) && !empty($this->attributes['legal_name'])) {
            return $this->attributes['legal_name'];
        }
        return $this->attributes['short_name'] ?? null;
    }

    /**
     * Get the nightly goal for a given date or day of week.
     * Falls back to base nightly_goal if daily goal is not set.
     *
     * @param string|\Carbon\Carbon|null $date
     * @return float|null
     */
    public function getGoalForDate($date = null): ?float
    {
        if (!$date) {
            return $this->nightly_goal !== null ? (float) $this->nightly_goal : null;
        }

        try {
            $dayName = strtolower(\Carbon\Carbon::parse($date)->format('l'));
        } catch (\Throwable $e) {
            return $this->nightly_goal !== null ? (float) $this->nightly_goal : null;
        }

        $goals = $this->nightly_goals;

        if (is_array($goals) && isset($goals[$dayName]) && $goals[$dayName] !== '' && $goals[$dayName] !== null) {
            return (float) $goals[$dayName];
        }

        return $this->nightly_goal !== null ? (float) $this->nightly_goal : null;
    }

    public function website()
    {
        return $this->belongsTo(Website::class, 'website_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'nr_user_locations', 'location_id', 'user_id')->withTimestamps();
    }

    public function nightlyReports()
    {
        return $this->hasMany(NrNightlyReport::class, 'location_id');
    }

    public function boutiqueReports()
    {
        return $this->hasMany(NrBoutiqueReport::class, 'location_id');
    }

    public function cohReports()
    {
        return $this->hasMany(NrCohReport::class, 'location_id');
    }

    public function incidents()
    {
        return $this->hasMany(NrIncident::class, 'location_id');
    }

    public function witnessStatements()
    {
        return $this->hasMany(NrWitnessStatement::class, 'location_id');
    }

    public function highTransactions()
    {
        return $this->hasMany(NrHighTransaction::class, 'location_id');
    }

    public function benchmark()
    {
        return $this->hasOne(NrBenchmark::class, 'location_id');
    }
}
