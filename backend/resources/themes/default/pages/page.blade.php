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
        {!! $page->content['body'] ?? '' !!}
    </div>

</div>

@endsection