@extends('layouts.admin')

@section('content')
<!-- Posts Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Posts List',
    'columns' => [
    ['label' => 'Thumbnail', 'field' => 'thumbnail', 'type' => 'image'], // Add this
    ['label' => 'Title', 'field' => 'title'],
    ],
    'collection' => $posts->map(function ($post) {
    return [
    'id' => $post->id,
    'title' => $post->title,
    'is_active' => $post->is_active,
    'is_featured' => $post->is_featured,
    'thumbnail' => $post->thumbnail?->file_path // Relative path
    ];
    }),
    'routes' => [
    'create' => 'admin.posts.create',
    'edit' => 'admin.posts.edit',
    'delete' => 'admin.posts.destroy'
    ],
    'primaryKey' => 'id',
    'statusFields' => [
    'is_active' => 'Active',
    'is_featured' => 'Featured'
    ]
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection