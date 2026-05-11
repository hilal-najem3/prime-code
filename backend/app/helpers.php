<?php

use Modules\Tenants\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Filesystem\FilesystemAdapter;
use Modules\Tenants\Models\Tenant;
use Modules\Languages\Models\Language;

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

if (!function_exists('logger')) {
    /**
     * Return the application logger instance.
     *
     * Allows using logger()->info(...), logger()->error(...), etc.
     */
    function logger()
    {
        return app('log');
    }
}

/*
|--------------------------------------------------------------------------
| Event Helper
|--------------------------------------------------------------------------
| Typed helper to avoid Intelephense false positives around event dispatch.
*/

if (!function_exists('dispatch_event')) {
    function dispatch_event(object|string $event, array $payload = [], bool $halt = false): mixed
    {
        /** @var Dispatcher $events */
        $events = app('events');

        return $events->dispatch($event, $payload, $halt);
    }
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

    if ($media->disk === 'private') {
        return route('media.secure', $media->id);
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

function tenant_connect(Tenant|string $tenant, ?string $search = null): void
{
    static $current = null;

    /*
    |--------------------------------------------------------------------------
    | Setup tenant variable
    |--------------------------------------------------------------------------
    */
    if (gettype($tenant) === 'string') {
        $tenant = cache()->remember(
            "tenant_lookup_{$search}_{$tenant}",
            60,
            fn() => Tenant::where($search ?? 'slug', $tenant)->firstOrFail()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Skip if already connected to same tenant
    |--------------------------------------------------------------------------
    */
    if (
        $current &&
        $current->id === $tenant->id &&
        DB::connection('tenant')->getDatabaseName() === $tenant->database
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Set connection config
    |--------------------------------------------------------------------------
    */
    Config::set('database.connections.tenant.database', $tenant->database);
    Config::set('database.connections.tenant.username', $tenant->db_username);

    if ($tenant->db_password) {
        Config::set('database.connections.tenant.password', $tenant->db_password);
    }

    /*
    |--------------------------------------------------------------------------
    | Reconnect only when needed
    |--------------------------------------------------------------------------
    */
    DB::purge('tenant');
    DB::reconnect('tenant');

    DB::setDefaultConnection('tenant');

    /*
    |--------------------------------------------------------------------------
    | Cache current tenant
    |--------------------------------------------------------------------------
    */
    $current = $tenant;

    app(TenantContext::class)->set($tenant);
}

function tenantLocale()
{
    return Language::where('is_default', true)
        ->value('slug') ?? 'en';
}

function resolve_route_model(string $routeKey, string $modelClass, ?Request $request = null)
{
    $request ??= request();

    $routeValue = $request->route($routeKey);

    if ($routeValue instanceof $modelClass) {
        return $routeValue;
    }

    return $modelClass::findOrFail($routeValue);
}

function trans_field(array $field, string $lang = null)
{
    $lang ??= tenantLocale();

    return $field[$lang]
        ?? $field['en']
        ?? array_values($field)[0]
        ?? null;
}