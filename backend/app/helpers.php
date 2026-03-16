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