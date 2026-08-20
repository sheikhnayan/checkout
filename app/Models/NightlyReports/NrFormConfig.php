<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;

class NrFormConfig extends Model
{
    protected $table = 'nr_form_configs';

    protected $fillable = [
        'report_type',
        'field_key',
        'label',
        'visible',
        'required',
        'sort_order',
        'hint',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'required' => 'boolean',
        'sort_order' => 'integer',
    ];
}
