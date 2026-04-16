<?php

namespace Modules\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
|--------------------------------------------------------------------------
| Page Model
|--------------------------------------------------------------------------
|
| Represents a dynamic page within a tenant website.
| Supports multilingual fields, SEO metadata, and layout binding.
|
*/

class Page extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'pages';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'title',
        'slug',
        'layout',
        'content',
        'status',
        'is_homepage',
        'meta_title',
        'meta_description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'content' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_homepage' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Published pages only
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: Homepage
     */
    public function scopeHomepage(Builder $query): Builder
    {
        return $query->where('is_homepage', true);
    }

    /**
     * Scope: Filter by slug (per language)
     */
    public function scopeBySlug(Builder $query, string $slug, string $lang = 'en'): Builder
    {
        return $query->where("slug->$lang", $slug);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Optional but Powerful)
    |--------------------------------------------------------------------------
    */

    /**
     * Get title for a specific language
     */
    public function getTitle(string $lang = 'en'): ?string
    {
        return $this->title[$lang]
            ?? $this->title['en']
            ?? array_values($this->title)[0]
            ?? null;
    }

    /**
     * Get slug for a specific language
     */
    public function getSlug(string $lang = 'en'): ?string
    {
        return $this->slug[$lang] ?? null;
    }

    /**
     * Get meta title for a specific language
     */
    public function getMetaTitle(string $lang = 'en'): ?string
    {
        return $this->meta_title[$lang] ?? null;
    }

    /**
     * Get meta description for a specific language
     */
    public function getMetaDescription(string $lang = 'en'): ?string
    {
        return $this->meta_description[$lang] ?? null;
    }
}