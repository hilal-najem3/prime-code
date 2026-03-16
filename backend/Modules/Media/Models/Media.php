<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'path',
        'filename',
        'extension',
        'mime_type',
        'size',
        'alt_text',
        'collection',
        'model_type',
        'model_id',
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Polymorphic Relationship
    |--------------------------------------------------------------------------
    */

    public function model()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Uploader
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | File URL
    |--------------------------------------------------------------------------
    */

    public function getUrlAttribute(): ?string
    {
        return media_url($this);
    }

    /*
    |--------------------------------------------------------------------------
    | File Delete Helper
    |--------------------------------------------------------------------------
    */

    public function deleteFile(): void
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}