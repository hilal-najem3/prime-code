<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = ['post_id', 'comment_id', 'user_id', 'content'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(PostComment::class, 'comment_id');
    }

    public function children()
    {
        return $this->hasMany(PostComment::class, 'comment_id');
    }

    public function replies()
    {
        return $this->hasMany(PostComment::class, 'comment_id');
    }
}