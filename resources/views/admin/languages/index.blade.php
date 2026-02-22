@extends('layouts.admin')

@section('content')
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Languages List',
    'columns' => [
    ['label' => 'Language Code', 'field' => 'code'],
    ['label' => 'Language Name', 'field' => 'name'],
    ],
    'collection' => $languages,
    'routes' => [
    'create' => 'admin.languages.create',
    'edit' => 'admin.languages.edit',
    'delete' => 'admin.languages.destroy',
    ],
    'primaryKey' => 'id',
    'statusField' => 'is_active',
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection