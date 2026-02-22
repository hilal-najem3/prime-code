<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    private string $defaultLocale = 'en';

    private function deviceInfo()
    {
        $agent = app('agent');
        return [
            'isMobile'  => $agent->isMobile(),
            'isTablet'  => $agent->isTablet(),
            'isDesktop' => !$agent->isMobile() && !$agent->isTablet(),
        ];
    }

    private function localizedRoute(string $name, array $params = []): string
    {
        try {
            if ($name === null) {
                return '#';
            }

            $locale = App::getLocale();

            // If NOT default → always include locale
            if ($locale !== $this->defaultLocale) {
                return route($name, array_merge(['locale' => $locale], $params));
            }

            // If default → try NON-localized route
            $nonLocalizedName = str_replace('localized.', '', $name);

            if (Route::has($nonLocalizedName)) {
                try {
                    return route($nonLocalizedName, $params); // no locale param
                } catch (\Exception $e) {
                    // ignore if missing params
                }
            }

            // fallback to "localized.*" but WITHOUT adding locale
            return route($name, $params);
        } catch (\Exception $e) {
            return '#';
        }
    }

    /**
     * Build navigation links dynamically.
     */
    private function buildLinks(string $activePage = null): array
    {
        $links = [
            'left' => [
                ['route' => 'home', 'text' => __('links.home'), 'tag' => '#',],
                ['route' => 'about', 'text' => __('links.about'), 'tag' => 'about',],
                ['route' => 'contact', 'text' => __('links.contact'), 'tag' => 'contact',],
            ],
            'right' => [],
        ];

        foreach ($links as $side => &$items) {
            foreach ($items as &$item) {

                // Section-based navigation
                if (!empty($item['tag'])) {
                    $item['url'] = $this->welcomeTagUrl($item['tag'], $activePage);

                    // active only on welcome page
                    $item['active'] = $activePage === 'welcome';
                    continue;
                }

                // External links
                if (!empty($item['external_url'])) {
                    $item['url'] = $item['external_url'];
                    $item['active'] = false;
                    continue;
                }

                // Normal routes
                if (!empty($item['route'])) {
                    $routeName = $this->resolveRouteName($item['route']);
                    $item['url'] = $this->localizedRoute($routeName);

                    // ✅ ACTIVE LOGIC
                    $item['active'] = Str::startsWith($activePage, $item['route']);
                }
            }
        }

        return $links;
    }


    private function resolveRouteName(string $name): string
    {
        if (app()->getLocale() === $this->defaultLocale) {
            return $name;
        }

        return 'localized.' . $name;
    }


    private function buildPageData(string $page, array $extra = []): array
    {
        $info  = $this->deviceInfo();
        $links = $this->buildLinks($page);

        return array_merge($info, [
            'leftLinks'  => $links['left'],
            'rightLinks' => $links['right'],
            'page'       => $page,
        ], $extra);
    }


    private function welcomeTagUrl(string $tag, string $page): string
    {
        // On welcome page → tag only
        if ($page === 'welcome') {
            return '#' . $tag;
        }

        $locale = app()->getLocale();

        // Default locale (en)
        if ($locale === $this->defaultLocale) {
            return route('welcome') . '#' . $tag;
        }

        // Localized
        return route('localized.welcome', ['locale' => $locale]) . '#' . $tag;
    }


    public function home()
    {
        return view(
            'main.welcome',
            $this->buildPageData('welcome')
        );
    }
}