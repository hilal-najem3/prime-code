<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Settings\Models\Setting;

/*
|--------------------------------------------------------------------------
| Settings Service
|--------------------------------------------------------------------------
|
| Handles all dynamic configuration logic:
| - Get / Set settings
| - Cache management
| - Bulk operations
|
*/

class SettingsService
{
    /**
     * Cache TTL (seconds)
     */
    protected int $ttl = 43200; // 12 hours

    /**
     * Get cache key for current tenant
     */
    protected function cacheKey(): string
    {
        return 'tenant_' . tenant_id() . '_settings';
    }

    /*
    |--------------------------------------------------------------------------
    | Core Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get all settings (cached)
     */
    public function all(?string $group = null): array
    {
        $settings = $this->getAllCached();

        if ($group) {
            return array_filter($settings, fn($item) => $item['group'] === $group);
        }

        return $settings;
    }

    /**
     * Get single setting
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAllCached();

        return $settings[$key]['value'] ?? $default;
    }

    /**
     * Set or update a setting
     */
    public function set(string $key, $value, string $type = 'string', ?string $group = null, bool $isPublic = false): Setting
    {
        // Normalize value based on type
        $value = $this->normalizeValue($value, $type);

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_public' => $isPublic,
            ]
        );

        $this->clearCache();

        return $setting;
    }

    /**
     * Bulk set settings
     */
    public function bulkSet(array $settings): void
    {
        foreach ($settings as $item) {

            Setting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'] ?? null,
                    'type' => $item['type'] ?? 'string',
                    'group' => $item['group'] ?? null,
                    'is_public' => $item['is_public'] ?? false,
                ]
            );
        }

        $this->clearCache();
    }

    /**
     * Delete a setting
     */
    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();

        $this->clearCache();
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Layer
    |--------------------------------------------------------------------------
    */

    /**
     * Get all settings from cache
     */
    protected function getAllCached(): array
    {
        return Cache::remember(
            $this->cacheKey(),
            $this->ttl,
            function () {

                return Setting::query()
                    ->get()
                    ->mapWithKeys(function ($setting) {

                        return [
                            $setting->key => [
                                'value' => $setting->value,
                                'type' => $setting->type,
                                'group' => $setting->group,
                                'is_public' => $setting->is_public,
                            ]
                        ];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey());
    }

    /*
    |--------------------------------------------------------------------------
    | Public Settings
    |--------------------------------------------------------------------------
    */

    /**
     * Get public settings only
     */
    public function public(): array
    {
        return collect($this->getAllCached())
            ->filter(fn($item) => $item['is_public'] === true)
            ->map(fn($item) => $item['value'])
            ->toArray();
    }

    protected function normalizeValue($value, string $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'number' => is_numeric($value) ? $value + 0 : 0,
            'json' => is_array($value) ? $value : (array) $value,
            default => $value,
        };
    }
}