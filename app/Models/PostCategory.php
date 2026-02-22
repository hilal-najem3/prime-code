<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use App\Models\Post;

class PostCategory extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'parent_id', 'thumbnail_id', 'is_active'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_post_category', 'category_id', 'post_id');
    }

    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PostCategory::class, 'parent_id');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}