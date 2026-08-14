<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HelpCenterPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'banner_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sections()
    {
        return $this->hasMany(HelpCenterSection::class)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function collaborators()
    {
        return $this->hasMany(HelpCenterCollaborator::class);
    }

    public function acceptedCollaborators()
    {
        return $this->hasMany(HelpCenterCollaborator::class)->where('status', 'accepted');
    }

    public function canUserEdit(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin() || $user->id === $this->user_id) return true;
        return $this->collaborators()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if (empty($base)) $base = 'help-center';
        $slug = $base;
        $count = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . Str::random(4);
            $count++;
        }
        return strtolower($slug);
    }
}
