@extends('layouts.admin')

@section('title', 'WAF Regeln')
@section('page-title', 'WAF — Regeln')
@section('page-subtitle', 'IP-Blacklists, CIDR-Blöcke und User-Agent Filter')

@section('page-actions')
    <a href="{{ route('admin.waf.rules.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Regel hinzufügen
    </a>
@endsection

@section('page-content')

@if(session('success'))
<div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-5 flex items-center gap-3">
    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <p class="text-sm text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Typ</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Wert</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktion</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Notiz</th>
                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktionen</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($rules as $rule)
            <tr class="hover:bg-gray-50 transition-colors {{ !$rule->active ? 'opacity-50' : '' }}">
                <td class="px-6 py-4">
                    @php
                        $typeColor = match($rule->type) { 'ip' => 'blue', 'cidr' => 'purple', 'useragent' => 'orange', default => 'gray' };
                        $typeLabel = match($rule->type) { 'ip' => 'IP', 'cidr' => 'CIDR', 'useragent' => 'User-Agent', default => $rule->type };
                    @endphp
                    <x-badge :color="$typeColor">{{ $typeLabel }}</x-badge>
                </td>
                <td class="px-6 py-4">
                    <code class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded font-mono">{{ $rule->value }}</code>
                </td>
                <td class="px-6 py-4">
                    <x-badge :color="$rule->action === 'block' ? 'red' : 'yellow'">
                        {{ $rule->action === 'block' ? 'Blockieren' : 'Challenge' }}
                    </x-badge>
                </td>
                <td class="px-6 py-4">
                    <x-badge :color="$rule->active ? 'green' : 'gray'">
                        {{ $rule->active ? 'Aktiv' : 'Inaktiv' }}
                    </x-badge>
                </td>
                <td class="px-6 py-4 text-gray-400 text-xs">{{ $rule->note ?? '—' }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <form method="POST" action="{{ route('admin.waf.rules.toggle', $rule) }}">
                            @csrf
                            <button type="submit"
                                    class="text-gray-400 hover:text-blue-600 transition-colors p-1.5 rounded-lg hover:bg-blue-50"
                                    title="{{ $rule->active ? 'Deaktivieren' : 'Aktivieren' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $rule->active ? 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z' : 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                </svg>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.waf.rules.destroy', $rule) }}"
                              x-data
                              @submit.prevent="if(confirm('Regel wirklich löschen?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1.5 rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3 text-gray-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-sm">Noch keine WAF-Regeln vorhanden</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($rules->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $rules->links() }}
    </div>
    @endif
</div>

@endsection
