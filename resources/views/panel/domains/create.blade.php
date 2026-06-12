@extends('layouts.panel')

@section('title', 'Domain hinzufügen')
@section('page-title', 'Domain hinzufügen')

@section('page-content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('panel.domains.store') }}">
            @csrf

            <x-domain-form :webservers="$webservers"/>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('panel.domains.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Abbrechen
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                    Domain hinzufügen
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
