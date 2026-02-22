@extends('layouts.admin')

@section('content')
<!-- Tags Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Tags List',
    'columns' => [
    ['label' => 'Name', 'field' => 'name'],
    ],
    'collection' => $tags,
    'routes' => [
    'create' => 'admin.project-tags.create',
    'edit' => 'admin.project-tags.edit',
    'delete' => 'admin.project-tags.destroy'
    ],
    'primaryKey' => 'id',
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection