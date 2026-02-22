<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProjectTag extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];
    public $translatable = ['name'];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_project_tag', 'tag_id', 'project_id');
    }
}