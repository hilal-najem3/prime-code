@props(['message' => 'Success!', 'color' => 'green'])
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
    class="fixed top-5 right-5 bg-{{ $color }}-500 text-white px-4 py-2 rounded shadow z-50 flex items-center space-x-4">
    <span>{{ $message }}</span>
    <button @click="show = false" class="text-white hover:text-red-200">&times;</button>
</div>