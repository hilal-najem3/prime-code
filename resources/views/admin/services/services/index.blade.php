@extends('layouts.admin')

@section('content')
<!-- Services Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Services List',
    'columns' => [
    ['label' => 'Thumbnail', 'field' => 'thumbnail', 'type' => 'image'], // Add this
    ['label' => 'Name', 'field' => 'name'],
    ],
    'collection' => $services->map(function ($service) {
    return [
    'id' => $service->id,
    'short_description' => $service->short_description,
    'description' => $service->description,
    'icon' => $service->icon,
    'slug' => $service->slug,
    'price' => $service->price,
    'currency_id' => $service->currency_id,
    'active' => $service->active,
    'thumbnail' => $service->thumbnail?->file_path // Relative path
    ];
    }),
    'routes' => [
    'create' => 'admin.services.create',
    'edit' => 'admin.services.edit',
    'delete' => 'admin.services.destroy'
    ],
    'primaryKey' => 'id',
    'statusField' => 'active'
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection