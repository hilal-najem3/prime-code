@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Service</h2>

    @include('admin.services.services._form', ['languages' => $languages, 'service' => $service])
</div>
@endsection