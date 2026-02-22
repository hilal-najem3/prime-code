<?php

namespace App\Models;

use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\PostTag;
use App\Models\Media;

class Post extends Model
{
    use HasTranslations;

    protected $fillable = [
        'link',
        'title',
        'content',
        'thumbnail_id',
        'is_active',
        'is_featured'
    ];
    public $translatable = ['title', 'content'];

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function categories()
    {
        return $this->belongsToMany(PostCategory::class, 'post_post_category', 'post_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(PostTag::class, 'post_post_tag', 'post_id', 'tag_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }
}