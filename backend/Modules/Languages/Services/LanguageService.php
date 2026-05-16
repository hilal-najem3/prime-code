<?php

namespace Modules\Languages\Services;

use Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Collection;

class LanguageService
{
    /**
     * Find language
     */
    public function find(string $id): Language
    {
        return Language::findOrFail($id);
    }

    /**
     * Get all languages
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Language::query();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        if (isset($filters['default'])) {
            $query->where('is_default', $filters['default']);
        }

        return $query->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active languages
     */
    public function getActive(): Collection
    {
        return Language::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get default language
     */
    public function getDefault(): ?Language
    {
        return Language::default()->first();
    }

    /**
     * Create new language
     */
    public function create(array $data): Language
    {
        /*
        |--------------------------------------------------------------------------
        | Creation
        |--------------------------------------------------------------------------
        */

        return Language::create($data);
    }

    /**
     * Update existing language
     */
    public function update(Language $language, array $data): Language
    {
        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $language->update($data);

        return $language->fresh();
    }

    /**
     * Delete language
     */
    public function delete(Language $language): void
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent deleting last language
        |--------------------------------------------------------------------------
        */

        if (Language::count() === 1) {
            throw new \Exception('Cannot delete the only language.');
        }

        /*
        |--------------------------------------------------------------------------
        | If deleting default → assign another one
        |--------------------------------------------------------------------------
        */

        if ($language->is_default) {
            $newDefault = Language::where('id', '!=', $language->id)->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $language->delete();
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Language $language): Language
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent disabling last active language
        |--------------------------------------------------------------------------
        */

        if ($language->is_active && Language::active()->count() === 1) {
            throw new \Exception('At least one active language is required.');
        }

        $language->update([
            'is_active' => !$language->is_active,
        ]);

        return $language->fresh();
    }

    /**
     * Set as default language
     */
    public function setDefault(Language $language): Language
    {
        /*
        |--------------------------------------------------------------------------
        | Reset all defaults (extra safety)
        |--------------------------------------------------------------------------
        */

        Language::where('is_default', true)->update(['is_default' => false]);

        $language->update([
            'is_default' => true,
        ]);

        return $language->fresh();
    }
}
