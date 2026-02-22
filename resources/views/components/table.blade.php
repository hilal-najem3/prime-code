@props([
'columns' => [],
'collection' => [],
'routes' => [
'create' => null,
'edit' => null,
'delete' => null,
],
'primaryKey' => 'id',
'statusField' => null,
'statusFields' => [],
])

@php
if (empty($statusFields) && $statusField) {
$statusFields = [$statusField => 'Active'];
}
@endphp

<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold text-gray-800">{{ $name ?? 'Items List'}}</h2>
    @if($routes['create'])
    <a href="{{ route($routes['create']) }}"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">
        + Create New
    </a>
    @endif
</div>

<!-- Search Input -->
<div class="mb-4">
    <input type="text" id="searchInput" placeholder="Search..."
        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300" />
</div>

@if(session('success'))
<x-ui.toast :message="session('success')" />
@endif

@if ($errors->any())
@foreach ($errors->all() as $error)
<x-ui.toast :message="$error" :color="'red'" />
@endforeach
@endif

<!-- Table -->
<div class="overflow-x-auto">
    <table id="itemsTable"
        class="min-w-full bg-white border border-gray-200 rounded-xl shadow-sm text-sm text-gray-700">
        <thead class="bg-gray-50">
            <tr>
                @foreach($columns as $col)
                <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $col['label'] }}</th>
                @endforeach
                @foreach($statusFields as $field => $label)
                <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $label }}</th>
                @endforeach
                <th class="px-4 py-3 text-left font-medium text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody id="tableBody" class="divide-y divide-gray-100">
            @forelse ($collection as $item)
            <tr class="hover:bg-gray-50 transition">
                @foreach($columns as $col)
                @php
                $value = $item[$col['field']] ?? '';
                if (is_array($value)) {
                $value = collect($value)->first(fn($v) => is_string($v) && !empty($v));
                }
                @endphp
                <td class="px-4 py-3">
                    @if(isset($col['type']) && $col['type'] === 'image')
                    @if(!empty($value))
                    <img src="{{ asset('storage/' . $value) }}" alt="Thumbnail"
                        class="w-10 h-10 rounded-full object-cover" />
                    @else
                    <span class="text-gray-400 italic">No Image</span>
                    @endif
                    @else
                    {{ $value }}
                    @endif
                </td>
                @endforeach

                @foreach($statusFields as $field => $label)
                <td class="px-4 py-3">
                    @if ($item[$field] ?? false)
                    <span class="inline-block px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        {{ $label }}
                    </span>
                    @else
                    <span class="inline-block px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                        Not {{ $label }}
                    </span>
                    @endif
                </td>
                @endforeach

                <td class="px-4 py-3 space-x-2">
                    @if($routes['edit'])
                    <a href="{{ route($routes['edit'], $item[$primaryKey]) }}"
                        class="text-blue-600 hover:underline">Edit</a>
                    @endif

                    @if($routes['delete'])
                    <form action="{{ route($routes['delete'], $item[$primaryKey]) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('Are you sure you want to delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($columns) + count($statusFields) + 1 }}"
                    class="px-4 py-4 text-center text-gray-400 italic">
                    No items found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>

@push('scripts')
<script src="{{ asset('assets/admin/js/table.js') }}"></script>
@endpush