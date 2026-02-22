@extends('layouts.admin')

@section('content')
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Attributes List',
    'columns' => [
    ['label' => 'Attribute Name', 'field' => 'name'],
    ['label' => 'Attribute Type', 'field' => 'type'],
    ],
    'collection' => $attributes,
    'routes' => [
    'create' => 'admin.attributes.create',
    'edit' => 'admin.attributes.edit',
    'delete' => 'admin.attributes.destroy',
    ],
    'primaryKey' => 'id',
    'statusFields' => [
    'is_required' => 'Required',
    ]
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection