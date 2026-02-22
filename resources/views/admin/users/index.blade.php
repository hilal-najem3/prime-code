@extends('layouts.admin')

@section('content')
<!-- Users Table -->
<div class="overflow-x-auto">
    @include('components.table', [
    'name' => 'Users List',
    'columns' => [
    ['label' => 'Thumbnail', 'field' => 'thumbnail', 'type' => 'image'], // Add this
    ['label' => 'Name', 'field' => 'name'],
    ['label' => 'Email', 'field' => 'email']
    ],
    'collection' => $users->map(function ($user) {
    return [
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'active' => $user->active,
    'thumbnail' => $user->thumbnail?->file_path // Relative path
    ];
    }),
    'routes' => [
    'create' => 'admin.users.create',
    'edit' => 'admin.users.edit',
    'delete' => 'admin.users.destroy'
    ],
    'primaryKey' => 'id',
    'statusField' => 'active'
    ])
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
@endsection