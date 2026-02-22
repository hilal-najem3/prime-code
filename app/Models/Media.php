<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['file_path', 'type'];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }
}