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
        'nightly_goal',
        'break_even',
        'historical_best',
        'active',
        'operating_days',
    ];

    protected $casts = [
        'active' => 'boolean',
        'nightly_goal' => 'decimal:2',
        'break_even' => 'decimal:2',
        'historical_best' => 'decimal:2',
        'operating_days' => 'array',
    ];

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
