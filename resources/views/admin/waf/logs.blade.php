@extends('layouts.admin')

@section('title', 'WAF Logs')
@section('page-title', 'WAF — Logs')
@section('page-subtitle', 'Zugriffsprotokolle der Web Application Firewall')

@section('page-content')

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Gesamt</p>
        <p class="text-xl font-semibold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Blockiert</p>
        <p class="text-xl font-semibold text-red-600 mt-1">{{ number_format($stats['blocked']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Challenge</p>
        <p class="text-xl font-semibold text-yellow-600 mt-1">{{ number_format($stats['challenged']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Durchgelassen</p>
        <p class="text-xl font-semibold text-green-600 mt-1">{{ number_format($stats['passed']) }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex items-center gap-3 flex-wrap">
        <input type="text" name="filter_ip" value="{{ request('filter_ip') }}"
               placeholder="IP-Adresse filtern"
               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
        <select name="filter_action"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
            <option value="">Alle Aktionen</option>
            <option value="blocked" @selected(request('filter_action') === 'blocked')>Blockiert</option>
            <option value="challenged" @selected(request('filter_action') === 'challenged')>Challenge</option>
            <option value="passed" @selected(request('filter_action') === 'passed')>Durchgelassen</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
            Filtern
        </button>
        @if(request('filter_ip') || request('filter_action'))
        <a href="{{ route('admin.waf.logs') }}" class="text-sm text-gray-500 hover:text-gray-700">
            Filter löschen
        </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Zeitstempel</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">IP</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktion</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Grund</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Domain</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">User-Agent</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-3 text-xs text-gray-400 font-mono whitespace-nowrap">
                    {{ $log->created_at->format('d.m.Y H:i:s') }}
                </td>
                <td class="px-6 py-3">
                    <code class="text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded font-mono">{{ $log->ip }}</code>
                </td>
                <td class="px-6 py-3">
                    @php
                        $actionColor = match($log->action) { 'blocked' => 'red', 'challenged' => 'yellow', 'passed' => 'green', default => 'gray' };
                        $actionLabel = match($log->action) { 'blocked' => 'Blockiert', 'challenged' => 'Challenge', 'passed' => 'Erlaubt', default => $log->action };
                    @endphp
                    <x-badge :color="$actionColor">{{ $actionLabel }}</x-badge>
                </td>
                <td class="px-6 py-3 text-xs text-gray-500 font-mono">{{ $log->reason ?? '—' }}</td>
                <td class="px-6 py-3 text-xs text-gray-600">{{ $log->domain?->name ?? '—' }}</td>
                <td class="px-6 py-3">
                    <p class="text-xs text-gray-400 truncate max-w-xs" title="{{ $log->user_agent }}">
                        {{ $log->user_agent ?? '—' }}
                    </p>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                    Noch keine WAF-Logs vorhanden.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
