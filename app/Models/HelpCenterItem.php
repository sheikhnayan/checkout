<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCenterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'help_center_section_id',
        'type',
        'title',
        'description',
        'custom_form_id',
        'url',
        'icon',
        'sort_order',
    ];

    public function section()
    {
        return $this->belongsTo(HelpCenterSection::class, 'help_center_section_id');
    }

    public function customForm()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }

    public function getResolvedUrlAttribute(): string
    {
        if ($this->type === 'form' && $this->customForm) {
            return route('forms.public.show', $this->customForm->slug);
        }
        return $this->url ?: '#';
    }

    public function getResolvedTitleAttribute(): string
    {
        if ($this->type === 'form' && $this->customForm) {
            return $this->title ?: $this->customForm->title;
        }
        return $this->title ?: 'Untitled Item';
    }
}
