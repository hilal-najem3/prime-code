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
    'create' => 'admin.post-tags.create',
    'edit' => 'admin.post-tags.edit',
    'delete' => 'admin.post-tags.destroy'
    ],
    'primaryKey' => 'id',
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection