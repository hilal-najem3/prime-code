<?php

namespace Modules\Pages\Services;

use Modules\Pages\Models\Page;

/*
|--------------------------------------------------------------------------
| Page Service
|--------------------------------------------------------------------------
|
| Handles all business logic related to pages:
| - Creation & updates
| - Slug uniqueness enforcement
| - Homepage management
|
*/

class PageService
{
    /*
    |--------------------------------------------------------------------------
    | Create Page
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Page
    {
        $this->ensureSlugUnique($data['slug']);

        if (!empty($data['is_homepage'])) {
            $this->resetHomepage();
        }

        return Page::create($data);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Page $page, array $data): Page
    {
        if (isset($data['slug'])) {
            $this->ensureSlugUnique($data['slug'], $page->id);
        }

        if (!empty($data['is_homepage'])) {
            $this->resetHomepage($page->id);
        }

        $page->update($data);

        return $page;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Page
    |--------------------------------------------------------------------------
    */

    public function delete(Page $page): void
    {
        $page->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Slug Validation
    |--------------------------------------------------------------------------
    */

    protected function ensureSlugUnique(array $slug, ?int $ignoreId = null): void
    {
        foreach ($slug as $lang => $value) {

            $exists = Page::query()
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->where("slug->$lang", $value)
                ->exists();

            if ($exists) {
                throw new \Exception("Slug already exists for [$lang]: $value");
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Homepage Logic
    |--------------------------------------------------------------------------
    */

    protected function resetHomepage(?int $exceptId = null): void
    {
        Page::query()
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->where('is_homepage', true)
            ->update(['is_homepage' => false]);
    }
}