@props(['icon', 'label'])

<div>
    <button data-toggle class="w-full flex items-center justify-between px-3 py-2 hover:bg-gray-100 rounded-md">
        <span class="flex items-center">
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
            <span class="ml-2">{{ $label }}</span>
        </span>
        <i data-lucide="chevron-right" class="w-4 h-4 transition-transform" data-arrow></i>
    </button>
    <div data-submenu class="ml-6 mt-1 space-y-1 hidden">
        {{ $slot }}
    </div>
</div>