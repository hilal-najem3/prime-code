@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Create New Project</h2>

    @include('admin.projects.projects._form', ['languages' => $languages, 'categories' => $categories, 'tags' => $tags,
    'isEdit' => false])
</div>
@endsection