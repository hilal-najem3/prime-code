<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('app.site_name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/main/img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/main/img/logo.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/main/img/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/main/img/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/main/img/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/main/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/main/img/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/main/img/android-chrome-512x512.png') }}">
    <link rel="manifest" href="{{ asset('assets/main/img/site.webmanifest') }}">

    <meta name="theme-color" content="#0F172A">
    <meta name="msapplication-TileColor" content="#0F172A">

    {{-- Google Fonts (Aligned with System) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?
        family=Plus+Jakarta+Sans:wght@400;600;700&
        family=Inter:wght@400;500;600&
        family=Cairo:wght@400;500;600;700&
        display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3.3 --}}
    <link href="{{ asset('assets/main/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/main/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    {{-- Main CSS --}}
    <link href="{{ asset('assets/main/css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="site-body">

    @include('main.partials.general.header')

    <main class="main-content">
        @yield('content')
    </main>

    @include('main.partials.general.footer')

    <div id="preloader">
        <div class="loader-wrapper">
            <div class="loader-line"></div>
        </div>
    </div>

    {{-- Scroll Top --}}
    <button id="scrollTop" class="scroll-top btn btn-primary position-fixed bottom-0 end-0 m-4 d-none">
        <i class="bi bi-arrow-up-short"></i>
    </button>

    {{-- Bootstrap JS --}}
    <script src="{{ asset('assets/main/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Main JS --}}
    <script src="{{ asset('assets/main/js/app.js') }}"></script>

    @stack('scripts')

</body>

</html>