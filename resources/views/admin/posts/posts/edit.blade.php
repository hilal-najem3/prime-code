@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Post</h2>

    @include('admin.posts.posts._form', ['languages' => $languages, 'categories' => $categories, 'tags' => $tags,
    'post' => $post,])
</div>
@endsection