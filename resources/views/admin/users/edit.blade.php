@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit User</h2>

    @include('admin.users._form', ['user' => $user])
</div>
@endsection