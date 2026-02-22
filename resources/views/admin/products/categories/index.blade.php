@extends('layouts.admin')

@section('content')
<!-- Categories Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Categories List',
    'columns' => [
    ['label' => 'Thumbnail', 'field' => 'thumbnail', 'type' => 'image'],
    ['label' => 'Name', 'field' => 'name'],
    ['label' => 'Parent', 'field' => 'parent_name'],
    ],
    'collection' => $categories->map(function ($category) {
    return [
    'id' => $category->id,
    'name' => $category->name,
    'parent_name' => $category->parent?->name ?? 'None',
    'is_active' => $category->is_active,
    'thumbnail' => $category->thumbnail?->file_path // Relative path
    ];
    }),
    'routes' => [
    'create' => 'admin.product-categories.create',
    'edit' => 'admin.product-categories.edit',
    'delete' => 'admin.product-categories.destroy'
    ],
    'primaryKey' => 'id',
    'statusField' => 'is_active',
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection