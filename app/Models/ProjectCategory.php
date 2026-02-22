<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Media;

class ProjectCategory extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'parent_id', 'thumbnail_id', 'is_active'];
    public $translatable = ['name'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_project_category', 'category_id', 'project_id');
    }
}