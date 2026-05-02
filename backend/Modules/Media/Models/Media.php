<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;
use App\Support\MediaConversionRegistry;

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

    /*
|--------------------------------------------------------------------------
| Image Variants
|--------------------------------------------------------------------------
*/

    public function getVariantsAttribute(): array
    {
        // Only for images
        if (!str_starts_with($this->mime_type, 'image/')) {
            return [];
        }

        $variants = MediaConversionRegistry::get($this->model_type);

        $result = [];

        foreach ($variants as $name => $width) {

            $variantPath = $this->variantPath($this->path, $name);

            if (Storage::disk($this->disk)->exists($variantPath)) {
                $result[$name] = Storage::disk($this->disk)->path($variantPath);
            }
        }

        return $result;
    }

    protected function variantPath(string $path, string $variant): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = pathinfo($path, PATHINFO_FILENAME);

        return dirname($path) . "/{$name}-{$variant}.{$extension}";
    }
}