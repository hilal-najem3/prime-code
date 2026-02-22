<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_description',
        'description',
        'icon',
        'slug',
        'price',
        'currency_id',
        'active',
        'thumbnail_id',
    ];

    protected $casts = [
        'name' => 'array',
        'short_description' => 'array',
        'description' => 'array',
        'active' => 'boolean',
        'price' => 'decimal:3',
    ];

    /**
     * Currency relationship
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Media: All associated media
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Thumbnail image
     */
    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    /**
     * Orders for this service
     */
    public function orders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    /**
     * Accessor to get name in current locale
     */
    public function getLocalizedNameAttribute()
    {
        return $this->name[app()->getLocale()] ?? collect($this->name)->first();
    }

    /**
     * Accessor to get short description in current locale
     */
    public function getLocalizedShortDescriptionAttribute()
    {
        return $this->short_description[app()->getLocale()] ?? collect($this->short_description)->first();
    }

    /**
     * Accessor to get full description in current locale
     */
    public function getLocalizedDescriptionAttribute()
    {
        return $this->description[app()->getLocale()] ?? collect($this->description)->first();
    }

    public function getTranslations(string $attribute): array
    {
        return $this->{$attribute} ?? [];
    }
}
