@props(['label', 'value', 'color' => 'blue'])

@php
$bgClass = match($color) {
    'green'  => 'bg-green-50',
    'purple' => 'bg-purple-50',
    'orange' => 'bg-orange-50',
    'red'    => 'bg-red-50',
    default  => 'bg-blue-50',
};
$iconClass = match($color) {
    'green'  => 'text-green-600',
    'purple' => 'text-purple-600',
    'orange' => 'text-orange-600',
    'red'    => 'text-red-600',
    default  => 'text-blue-600',
};
@endphp

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $value }}</p>
        </div>
        @isset($icon)
        <div class="w-12 h-12 rounded-xl {{ $bgClass }} flex items-center justify-center {{ $iconClass }}">
            {{ $icon }}
        </div>
        @endisset
    </div>
    @isset($footer)
    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
        {{ $footer }}
    </div>
    @endisset
</div>
