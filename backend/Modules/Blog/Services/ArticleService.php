<?php

namespace Modules\Blog\Services;

use Modules\Blog\Models\Article;
use Modules\Media\Services\MediaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

/*
|--------------------------------------------------------------------------
| Article Service
|--------------------------------------------------------------------------
|
| Handles all business logic related to articles:
| - Create / Update / Delete
| - Slug generation & validation
| - Publishing logic
| - Media attachment
|
*/

class ArticleService
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /*
    |--------------------------------------------------------------------------
    | Find Article
    |--------------------------------------------------------------------------
    */

    public function find(string $id): Article
    {
        return Article::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Article
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Article
    {
        return DB::transaction(function () use ($data) {

            // Generate slug if missing
            $data['slug'] = $this->generateSlugs($data['title'], $data['slug'] ?? []);

            // Create article
            $article = Article::create($data);

            // Attach featured image
            if (!empty($data['featured_media_id'])) {
                $this->attachFeatured($article, $data['featured_media_id']);
            }

            // Attach gallery
            if (!empty($data['gallery_ids'])) {
                $this->attachGallery($article, $data['gallery_ids']);
            }

            return $article;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Update Article
    |--------------------------------------------------------------------------
    */

    public function update(Article $article, array $data): Article
    {
        return DB::transaction(function () use ($article, $data) {

            // Regenerate slug if title changed
            if (isset($data['title'])) {
                $data['slug'] = $this->generateSlugs($data['title'], $data['slug'] ?? []);
            }

            $article->update($data);

            // Update featured image
            if (array_key_exists('featured_media_id', $data)) {
                $this->attachFeatured($article, $data['featured_media_id']);
            }

            // Update gallery
            if (array_key_exists('gallery_ids', $data)) {
                $this->syncGallery($article, $data['gallery_ids']);
            }

            return $article;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Article
    |--------------------------------------------------------------------------
    */

    public function delete(Article $article): void
    {
        DB::transaction(function () use ($article) {

            $article->delete();

            // Optional: delete media if needed later
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Slug Generation
    |--------------------------------------------------------------------------
    */

    protected function generateSlugs(array $titles, array $existingSlugs = []): array
    {
        $slugs = [];

        foreach ($titles as $locale => $title) {

            $baseSlug = $existingSlugs[$locale] ?? Str::slug($title);

            $slug = $this->ensureUniqueSlug($baseSlug, $locale);

            $slugs[$locale] = $slug;
        }

        return $slugs;
    }

    protected function ensureUniqueSlug(string $slug, string $locale): string
    {
        $original = $slug;
        $counter = 1;

        while (
            Article::where("slug->{$locale}", $slug)->exists()
        ) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Media Handling
    |--------------------------------------------------------------------------
    */

    protected function attachFeatured(Article $article, string $mediaId): void
    {
        $media = $this->findMedia($mediaId);

        $this->mediaService->duplicate($media, $article, 'featured');
    }

    protected function attachGallery(Article $article, array $mediaIds): void
    {
        foreach ($mediaIds as $id) {
            $media = $this->findMedia($id);
            $this->mediaService->duplicate($media, $article, 'gallery');
        }
    }

    protected function syncGallery(Article $article, array $mediaIds): void
    {
        // Remove old gallery
        foreach ($article->gallery as $media) {
            $this->mediaService->delete($media);
        }

        // Add new gallery
        $this->attachGallery($article, $mediaIds);
    }

    protected function findMedia(string $id)
    {
        return \Modules\Media\Models\Media::findOrFail($id);
    }
}
