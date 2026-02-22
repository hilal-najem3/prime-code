<footer class="pc-footer section section-dark pt-5 pb-4">
    <div class="container">

        <div class="row gy-4">

            {{-- Brand / About --}}
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-title mb-3">
                    {{ __('app.site_name') }}
                </h5>

                <p class="footer-text">
                    {{ __('footer.description') }}
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-subtitle mb-3">
                    {{ __('footer.quick_links') }}
                </h6>

                <ul class="footer-links list-unstyled">

                    @foreach ($leftLinks as $link)
                    <li class="mb-2">
                        <a href="{{ $link['url'] }}" class="footer-link">
                            {{ $link['text'] }}
                        </a>
                    </li>
                    @endforeach

                </ul>
            </div>

            {{-- Contact --}}
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-subtitle mb-3">
                    {{ __('footer.contact') }}
                </h6>

                <ul class="footer-links list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        info@prime-codes.com
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        +961 XX XXX XXX
                    </li>
                </ul>
            </div>

            {{-- CTA --}}
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-subtitle mb-3">
                    {{ __('footer.cta_title') }}
                </h6>

                <p class="footer-text mb-3">
                    {{ __('footer.cta_description') }}
                </p>

                <a href="#contact" class="btn btn-primary">
                    {{ __('footer.cta_button') }}
                </a>
            </div>

        </div>

        {{-- Divider --}}
        <hr class="footer-divider my-4">

        {{-- Bottom --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

            <p class="mb-2 mb-md-0 small">
                © {{ now()->year }} {{ __('app.site_name') }}.
                {{ __('footer.rights') }}
            </p>

            <div class="d-flex align-items-center gap-4">

                {{-- Language Switcher --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
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

                {{-- Social --}}
                <div class="footer-social d-flex gap-3">
                    <a href="#" class="footer-social-link">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <a href="#" class="footer-social-link">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>

            </div>

        </div>

    </div>
</footer>