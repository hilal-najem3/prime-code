@extends('layouts.admin')

@section('content')
<!-- Posts Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Projects List',
    'columns' => [
    ['label' => 'Thumbnail', 'field' => 'thumbnail', 'type' => 'image'], // Add this
    ['label' => 'Title', 'field' => 'title'],
    ],
    'collection' => $projects->map(function ($project) {
    return [
    'id' => $project->id,
    'title' => $project->title,
    'is_active' => $project->is_active,
    'is_featured' => $project->is_featured,
    'thumbnail' => $project->thumbnail?->file_path // Relative path
    ];
    }),
    'routes' => [
    'create' => 'admin.projects.create',
    'edit' => 'admin.projects.edit',
    'delete' => 'admin.projects.destroy'
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