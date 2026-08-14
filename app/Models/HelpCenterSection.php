<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCenterSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'help_center_page_id',
        'title',
        'description',
        'sort_order',
    ];

    public function page()
    {
        return $this->belongsTo(HelpCenterPage::class, 'help_center_page_id');
    }

    public function items()
    {
        return $this->hasMany(HelpCenterItem::class)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}
