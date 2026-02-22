@props([
'collection' => collect(),
'columns' => [],
'routes' => [
'create' => null,
'show' => null,
'edit' => null,
'delete' => null,
],
'name' => 'Items',
])

<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">{{ $name }}</h2>
    @if($routes['create'])
    <a href="{{ route($routes['create']) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        + Create New
    </a>
    @endif
</div>

<table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-gray-100 text-left">
        <tr>
            @foreach ($columns as $col)
            <th class="px-6 py-3 text-sm font-medium text-gray-700">{{ $col['label'] ?? ucfirst($col['field']) }}</th>
            @endforeach
            <th class="px-6 py-3 text-sm font-medium text-gray-700">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 text-sm">
        @forelse ($collection as $item)
        <tr>
            @foreach ($columns as $col)
            @php
            $value = data_get($item, $col['field'], '-');
            if (is_array($value)) {
            $value = collect($value)->first(fn($v) => is_string($v) && !empty($v));
            }
            @endphp
            <td class="px-6 py-4">{{ $value }}</td>
            @endforeach
            <td class="px-6 py-4 space-x-2">
                @if($routes['show'] ?? false)
                <a href="{{ route($routes['show'], $item->id) }}" class="text-blue-600 hover:underline">View</a>
                @endif
                @if($routes['edit'] ?? false)
                <a href="{{ route($routes['edit'], $item->id) }}" class="text-green-600 hover:underline">Edit</a>
                @endif
                @if($routes['delete'] ?? false)
                <form action="{{ route($routes['delete'], $item->id) }}" method="POST" class="inline-block"
                    onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ count($columns) + 1 }}" class="px-6 py-4 text-center text-gray-500">No items found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $collection->links() }}
</div>