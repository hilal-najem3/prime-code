<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">

<head>
    <meta charset="UTF-8">

    <title>
        {{ $page->getMetaTitle($lang) ?? $page->getTitle($lang) }}
    </title>

    <meta name="description" content="{{ $page->getMetaDescription($lang) }}">
</head>

<body>

    @yield('content')

</body>

</html>