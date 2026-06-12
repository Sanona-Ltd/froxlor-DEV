@props(['href', 'active' => false])

<a href="{{ $href }}"
   {{ $attributes->class([
       'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
       'bg-blue-50 text-blue-700' => $active,
       'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => !$active,
   ]) }}>
    @isset($icon)
    <span class="flex-shrink-0 w-5 h-5">{{ $icon }}</span>
    @endisset
    <span>{{ $slot }}</span>
</a>
