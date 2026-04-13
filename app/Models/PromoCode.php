<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    public const AUDIENCE_CLUB = 'club';
    public const AUDIENCE_AFFILIATE = 'affiliate';
    public const AUDIENCE_ENTERTAINER = 'entertainer';

    public const ALLOWED_AUDIENCES = [
        self::AUDIENCE_CLUB,
        self::AUDIENCE_AFFILIATE,
        self::AUDIENCE_ENTERTAINER,
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function getTargetNameAttribute(): ?string
    {
        if ($this->audience === self::AUDIENCE_AFFILIATE && $this->affiliate) {
            return $this->affiliate->display_name ?: optional($this->affiliate->user)->name;
        }

        if ($this->audience === self::AUDIENCE_ENTERTAINER && $this->entertainer) {
            return $this->entertainer->display_name ?: optional($this->entertainer->user)->name;
        }

        return null;
    }
}
