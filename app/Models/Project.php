<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Media;

class Project extends Model
{
    use HasTranslations;

    protected $fillable = [
        'link',
        'title',
        'content',
        'thumbnail_id',
        'is_active',
        'is_featured',
        'sort_order',
        'links' // Optional links for the project
    ];
    public $translatable = ['title', 'content'];

    protected $casts = [
        'links' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_project_category', 'project_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ProjectTag::class, 'project_project_tag', 'project_id', 'tag_id');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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