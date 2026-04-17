<?php

namespace Modules\Pages\Controllers;

use Illuminate\Routing\Controller;
use Modules\Pages\Models\Page;

/*
|--------------------------------------------------------------------------
| Page Resolver Controller
|--------------------------------------------------------------------------
|
| Responsible for:
| - Resolving pages dynamically using slug
| - Handling homepage fallback
| - Rendering pages using Blade themes
|
| This controller is used for PUBLIC WEBSITE rendering,
| not for admin API.
|
*/

class PageResolverController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Resolve Page
    |--------------------------------------------------------------------------
    |
    | Determines which page to display based on:
    | - Provided slug
    | - Homepage if slug is null
    |
    */

    public function resolve(?string $slug = null)
    {
        $lang = tenantLocale();

        /*
        |--------------------------------------------------------------------------
        | Homepage Resolution
        |--------------------------------------------------------------------------
        */

        if (!$slug) {
            $page = Page::query()
                ->published()
                ->homepage()
                ->firstOrFail();
        }

        /*
        |--------------------------------------------------------------------------
        | Slug Resolution
        |--------------------------------------------------------------------------
        */ else {
            $page = Page::query()
                ->published()
                ->bySlug($slug, $lang)
                ->firstOrFail();
        }

        /*
        |--------------------------------------------------------------------------
        | Render Page using Theme
        |--------------------------------------------------------------------------
        */

        return view($this->resolveView(), [
            'page' => $page,
            'lang' => $lang,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Theme View
    |--------------------------------------------------------------------------
    |
    | Determines which Blade view should render the page.
    | This will later support dynamic tenant themes.
    |
    */

    protected function resolveView(): string
    {
        return 'themes.default.page';
    }
}