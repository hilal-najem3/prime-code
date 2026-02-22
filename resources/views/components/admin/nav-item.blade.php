@props(['icon', 'label', 'route'])

<a href="{{ route($route) }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-100">
    <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
    <span class="ml-2">{{ $label }}</span>
</a>