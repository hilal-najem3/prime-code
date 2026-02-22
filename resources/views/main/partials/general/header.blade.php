<header id="siteHeader" class="navbar navbar-expand-lg fixed-top navbar-light pc-navbar">
    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center fw-bold"
            href="{{ route(app()->getLocale() === 'en' ? 'home' : 'localized.home', app()->getLocale() === 'en' ? [] : ['locale' => app()->getLocale()]) }}">
            <img src="{{ asset('assets/main/img/logo.png') }}" alt="PrimeCodes" height="36" class="me-2">
            {{-- <span class="brand-text">PrimeCodes</span> --}}
        </a>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Links --}}
        <div class="collapse navbar-collapse justify-content-between" id="mainNavbar">

            {{-- Left Links --}}
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

                @foreach ($leftLinks as $link)
                <li class="nav-item">
                    <a href="{{ $link['url'] }}" class="nav-link px-3 {{ $link['active'] ? 'active' : '' }}">
                        {{ $link['text'] }}
                    </a>
                </li>
                @endforeach

            </ul>

            <div class="d-flex align-items-center gap-3">

                {{-- Language Switcher --}}
                <div class="dropdown">
                    <button class="btn btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ strtoupper(app()->getLocale()) }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">

                        @foreach(config('locales.locales', ['en','ar']) as $locale)

                        @php
                        $isDefault = $locale === 'en';
                        $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
                        $params = request()->route()?->parameters() ?? [];

                        if (!$isDefault) {
                        $params['locale'] = $locale;
                        $routeName = 'localized.' . str_replace('localized.', '', $currentRoute);
                        } else {
                        unset($params['locale']);
                        $routeName = str_replace('localized.', '', $currentRoute);
                        }

                        $url = \Illuminate\Support\Facades\Route::has($routeName)
                        ? route($routeName, $params)
                        : route('home');
                        @endphp

                        <li>
                            <a class="dropdown-item {{ app()->getLocale() === $locale ? 'active' : '' }}"
                                href="{{ $url }}">
                                {{ strtoupper($locale) }}
                            </a>
                        </li>

                        @endforeach

                    </ul>
                </div>

                {{-- CTA --}}
                <a href="#contact" class="btn btn-primary">
                    {{ __('links.start') }}
                </a>

            </div>

        </div>
    </div>
</header>