@extends('layouts.admin')

@section('title', 'WAF Regel hinzufügen')
@section('page-title', 'WAF — Regel hinzufügen')

@section('page-content')

<div class="max-w-xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.waf.rules.store') }}">
            @csrf

            <div class="space-y-5">

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">Regeltyp</label>
                    <select id="type" name="type"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="ip"        @selected(old('type') === 'ip')>IP-Adresse</option>
                        <option value="cidr"      @selected(old('type') === 'cidr')>CIDR-Block (IP-Bereich)</option>
                        <option value="useragent" @selected(old('type') === 'useragent')>User-Agent Muster</option>
                    </select>
                    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1.5">Wert</label>
                    <input id="value" type="text" name="value" value="{{ old('value') }}"
                           placeholder="z.B. 1.2.3.4 oder 10.0.0.0/8 oder sqlmap"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono">
                    @error('value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700 mb-1.5">Aktion</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:border-red-300 transition-colors has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                            <input type="radio" name="action" value="block" @checked(old('action', 'block') === 'block')
                                   class="mt-0.5 text-red-600 focus:ring-red-500">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Blockieren</p>
                                <p class="text-xs text-gray-500 mt-0.5">Zugriff sofort verweigern (403)</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:border-yellow-300 transition-colors has-[:checked]:border-yellow-400 has-[:checked]:bg-yellow-50">
                            <input type="radio" name="action" value="challenge" @checked(old('action') === 'challenge')
                                   class="mt-0.5 text-yellow-600 focus:ring-yellow-500">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Challenge</p>
                                <p class="text-xs text-gray-500 mt-0.5">JS-Challenge anzeigen</p>
                            </div>
                        </label>
                    </div>
                    @error('action')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Notiz <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input id="note" type="text" name="note" value="{{ old('note') }}"
                           placeholder="z.B. 'Bekannter Scanner' oder 'Brute-Force Angriff'"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('note')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.waf.rules') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Abbrechen
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                    Regel speichern
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
