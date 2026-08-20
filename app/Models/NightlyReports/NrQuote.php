<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;

class NrQuote extends Model
{
    protected $table = 'nr_quotes';

    protected $fillable = [
        'quote_text',
        'author',
        'category',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
