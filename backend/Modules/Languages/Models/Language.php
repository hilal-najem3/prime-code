<?php

namespace Modules\Languages\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'direction',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Add scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Add mutators on create or update if default is set to true, set all others to false
    protected static function booted()
    {
        static::saving(function ($language) {
            if ($language->is_default) {
                static::where('is_default', true)->update(['is_default' => false]);
            }
        });
    }
}
