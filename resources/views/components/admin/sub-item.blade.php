@props(['label', 'route'])

<a href="{{ route($route) }}" class="block px-2 py-1 rounded hover:bg-gray-100">
    {{ $label }}
</a>