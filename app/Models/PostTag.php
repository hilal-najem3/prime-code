<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Post;
use Spatie\Translatable\HasTranslations;

class PostTag extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_post_tag', 'tag_id', 'post_id');
    }
}