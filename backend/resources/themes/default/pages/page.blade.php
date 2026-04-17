{{--
|--------------------------------------------------------------------------
| Page Template
|--------------------------------------------------------------------------
|
| Renders a dynamic page from the database
|
--}}

@extends('themes.default.layouts.app')

@section('content')

<div class="container py-5">

    {{-- Page Title --}}
    <h1 class="mb-4">
        {{ $page->getTitle($lang) }}
    </h1>

    {{-- Page Content --}}
    <div>
        @foreach ($page->content ?? [] as $block)

        @php
        $view = 'themes.default.blocks.'
        . $block['type']
        . '.'
        . ($block['variant'] ?? 'default');
        @endphp

        @includeIf($view, [
        'data' => $block['data'],
        'settings' => $block['settings'] ?? [],
        'lang' => $lang
        ])

        @endforeach
    </div>

</div>

@endsection