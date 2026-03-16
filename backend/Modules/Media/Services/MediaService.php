<?php

namespace Modules\Media\Services;

use Modules\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\FileNameGenerator;
use App\Support\MediaPathGenerator;

class MediaService
{
    protected array $collectionRules = [

        'avatar' => [
            'single' => true,
        ],

        'featured' => [
            'single' => true,
        ],

        'gallery' => [
            'single' => false,
        ],

        'attachments' => [
            'single' => false,
        ],

    ];

    /**
     * Upload a file and optionally attach it to a model.
     */
    public function upload(
        UploadedFile $file,
        string $directory,
        ?string $collection = null,
        $model = null,
        ?int $userId = null
    ): Media {

        return DB::transaction(function () use ($file, $directory, $collection, $model, $userId) {

            $disk = config('filesystems.default');

            $filename = FileNameGenerator::generate($file->getClientOriginalName());

            $directory = MediaPathGenerator::generate($directory, $collection);

            $path = $file->storeAs(
                $directory,
                $filename,
                $disk
            );

            $media = Media::create([
                'disk'       => $disk,
                'path'       => $path,
                'filename'   => $filename,
                'extension'  => $file->getClientOriginalExtension(),
                'mime_type'  => $file->getMimeType(),
                'size'       => $file->getSize(),
                'collection' => $collection,
                'user_id'    => $userId,
                'model_type' => $model ? get_class($model) : null,
                'model_id'   => $model ? $model->id : null,
            ]);

            return $media;
        });
    }

    /**
     * Delete media record and file from disk.
     */
    public function delete(Media $media): void
    {
        DB::transaction(function () use ($media) {

            $disk = $media->disk;
            $path = $media->path;

            // Delete the media row
            $media->delete();

            // Check if another record uses the same file
            $exists = Media::where('disk', $disk)
                ->where('path', $path)
                ->exists();

            // Only delete physical file if no reference remains
            if (!$exists) {
                Storage::disk($disk)->delete($path);
            }
        });
    }

    /**
     * Attach media to a model after upload.
     */
    public function attach(Media $media, $model, ?string $collection = null): Media
    {
        $media->update([
            'model_type' => get_class($model),
            'model_id'   => $model->id,
            'collection' => $collection ?? $media->collection
        ]);

        return $media;
    }

    public function duplicate(Media $media, $model, ?string $collection = null): Media
    {
        return Media::create([
            'disk'       => $media->disk,
            'path'       => $media->path,
            'filename'   => $media->filename,
            'extension'  => $media->extension,
            'mime_type'  => $media->mime_type,
            'size'       => $media->size,
            'collection' => $collection,
            'model_type' => get_class($model),
            'model_id'   => $model->id,
            'user_id'    => user_id(),
        ]);
    }

    protected function enforceCollectionRules($model, ?string $collection): void
    {
        if (!$model || !$collection) {
            return;
        }

        $rules = $this->collectionRules[$collection] ?? null;

        if (!$rules) {
            return;
        }

        if ($rules['single']) {

            $existing = Media::where('model_type', get_class($model))
                ->where('model_id', $model->id)
                ->where('collection', $collection)
                ->get();

            foreach ($existing as $media) {
                $this->delete($media);
            }
        }
    }
}