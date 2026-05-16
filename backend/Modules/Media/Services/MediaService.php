<?php

namespace Modules\Media\Services;

use Modules\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\FileNameGenerator;
use App\Support\MediaPathGenerator;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaService
{
    protected ImageVariantService $imageVariantService;
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

    public function __construct(ImageVariantService $imageVariantService)
    {
        $this->imageVariantService = $imageVariantService;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Media List
    |--------------------------------------------------------------------------
    */

    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Media::query();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['collection'])) {
            $query->where('collection', $filters['collection']);
        }

        if (!empty($filters['search'])) {
            $query->where('filename', 'like', '%' . $filters['search'] . '%');
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        return $query->latest()->paginate(
            $filters['per_page'] ?? 15
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Single Media
    |--------------------------------------------------------------------------
    */

    public function findOrFail(int $id): Media
    {
        return Media::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Media Usage
    |--------------------------------------------------------------------------
    */

    public function usage(Media $media)
    {
        return Media::where('disk', $media->disk)
            ->where('path', $media->path)
            ->get()
            ->map(function ($item) {
                return [
                    'media_id'   => $item->id,
                    'model_type' => $item->model_type,
                    'model_id'   => $item->model_id,
                    'collection' => $item->collection,
                ];
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Get File Stream (Private Media)
    |--------------------------------------------------------------------------
    */

    public function getFileStream(Media $media)
    {
        if (!Storage::disk($media->disk)->exists($media->path)) {
            abort(404);
        }

        return Storage::download(
            $media->path,
            $media->filename,
            [
                'Content-Type' => $media->mime_type,
            ],
            $media->disk
        );
    }

    /**
     * Get a streaming preview for a private media file.
     *
     * This method returns the file contents inline instead of forcing download.
     * It preserves the original mime type, length, filename, and cache headers.
     */
    public function getFilePreviewStream(Media $media)
    {
        if (!Storage::disk($media->disk)->exists($media->path)) {
            abort(404);
        }

        $stream = Storage::disk($media->disk)->readStream($media->path);

        if ($stream === false) {
            abort(404);
        }

        return response()->stream(
            fn() => fpassthru($stream),
            200,
            [
                'Content-Type' => $media->mime_type,
                'Content-Length' => $media->size,
                'Content-Disposition' => 'inline; filename="' . $media->filename . '"',
                'Cache-Control' => 'private, max-age=86400',
            ]
        );
    }

    /**
     * Upload a file and optionally attach it to a model.
     */
    public function upload(
        UploadedFile $file,
        string $directory,
        ?string $collection = null,
        $model = null,
        ?int $userId = null,
        ?string $disk = null
    ): Media {

        return DB::transaction(function () use ($file, $directory, $collection, $model, $userId, $disk) {

            $disk = $disk ?? config('filesystems.default');

            $filename = FileNameGenerator::generate($file->getClientOriginalName());

            $directory = MediaPathGenerator::generate($directory, $collection);

            $path = $file->storeAs(
                $directory,
                $filename,
                $disk
            );

            $this->imageVariantService->generate(
                $disk,
                $path,
                $model ? get_class($model) : null
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
            $modelType = $media->model_type;

            $media->delete();

            $exists = Media::where('disk', $disk)
                ->where('path', $path)
                ->exists();

            if (!$exists) {

                Storage::disk($disk)->delete($path);

                // Get all registered variants for this model type (or use default)
                $variants = \App\Support\MediaConversionRegistry::get($modelType);

                foreach ($variants as $name => $width) {

                    $variantPath = $this->variantPath($path, $name);

                    Storage::disk($disk)->delete($variantPath);
                }
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

    protected function variantPath(string $path, string $variant): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = pathinfo($path, PATHINFO_FILENAME);

        return dirname($path) . "/{$name}-{$variant}.{$extension}";
    }
}
