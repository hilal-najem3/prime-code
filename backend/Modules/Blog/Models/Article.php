<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\Media\Models\Media;

/*
|--------------------------------------------------------------------------
| Article Model
|--------------------------------------------------------------------------
|
| Represents blog articles for tenants.
| Supports multilingual JSON fields and media integration.
|
*/

class Article extends Model
{
    use SoftDeletes;

    protected $table = 'articles';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'published_at',
        'author_id',
        'meta_title',
        'meta_description',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts (JSON → Array)
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'title'            => 'array',
        'slug'             => 'array',
        'content'          => 'array',
        'excerpt'          => 'array',
        'meta_title'       => 'array',
        'meta_description' => 'array',
        'published_at'     => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /*
    |--------------------------------------------------------------------------
    | Media Relationships (Polymorphic)
    |--------------------------------------------------------------------------
    */

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function featuredImage()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection', 'featured');
    }

    public function gallery()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection', 'gallery');
    }

    public function attachments()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection', 'attachments');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors (Helper Methods)
    |--------------------------------------------------------------------------
    */

    public function getTitle(string $locale = 'en'): ?string
    {
        return $this->title[$locale] ?? null;
    }

    public function getSlug(string $locale = 'en'): ?string
    {
        return $this->slug[$locale] ?? null;
    }

    public function getExcerpt(string $locale = 'en'): ?string
    {
        return $this->excerpt[$locale] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at
            && $this->published_at <= now();
    }
}