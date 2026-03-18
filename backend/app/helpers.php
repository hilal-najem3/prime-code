<?php

use Modules\Tenants\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

/*
|--------------------------------------------------------------------------
| Tenant Helpers
|--------------------------------------------------------------------------
*/

function tenant()
{
    return app(TenantContext::class)->get();
}

function tenant_id()
{
    return app(TenantContext::class)->id();
}

/*
|--------------------------------------------------------------------------
| Authentication Helpers
|--------------------------------------------------------------------------
| These helpers wrap Laravel auth() to avoid static analysis errors
| from Intelephense when using auth()->user() or auth()->id().
*/

/** @noinspection PhpUndefinedMethodInspection */
function auth_user()
{
    return Auth::user();
}

/** @noinspection PhpUndefinedMethodInspection */
function user_id(): int|string|null
{
    return Auth::user()?->id;
}

/*
|--------------------------------------------------------------------------
| Media Helpers
|--------------------------------------------------------------------------
| Provides consistent URL generation and hides filesystem logic.
*/

function media_url($media): ?string
{
    if (!$media) {
        return null;
    }

    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk($media->disk);

    return $disk->url($media->path);
}

function media_variant_url($media, string $variant): ?string
{
    if (!$media) {
        return null;
    }

    $extension = pathinfo($media->path, PATHINFO_EXTENSION);
    $name = pathinfo($media->path, PATHINFO_FILENAME);

    $variantPath = dirname($media->path) . "/{$name}-{$variant}.{$extension}";

    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk($media->disk);

    return $disk->url($variantPath);
}

if (!function_exists('setting')) {

    function setting(string $key, $default = null)
    {
        return app(\Modules\Settings\Services\SettingsService::class)
            ->get($key, $default);
    }
}

function setting_lang($key, $lang = 'en', $default = null)
{
    $value = setting($key);

    return $value[$lang] ?? $default;
}

if (!function_exists('seo')) {

    function seo(string $key, ?string $lang = null, $default = null)
    {
        $value = setting("seo.$key", $default);

        if (is_array($value) && $lang) {
            return $value[$lang] ?? $default;
        }

        return $value;
    }
}