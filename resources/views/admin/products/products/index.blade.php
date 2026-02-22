@extends('layouts.admin')

@section('content')
<!-- products Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Products List',
    'columns' => [
    ['label' => 'Thumbnail', 'field' => 'thumbnail', 'type' => 'image'], // Add this
    ['label' => 'Title', 'field' => 'title'],
    ],
    'collection' => $products->map(function ($product) {
    return [
    'id' => $product->id,
    'title' => $product->title,
    'is_active' => $product->is_active,
    'is_featured' => $product->is_featured,
    'thumbnail' => $product->thumbnail?->file_path // Relative path
    ];
    }),
    'routes' => [
    'create' => 'admin.products.create',
    'edit' => 'admin.products.edit',
    'delete' => 'admin.products.destroy'
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