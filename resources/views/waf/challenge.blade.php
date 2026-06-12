<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verbindung wird überprüft…</title>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .spin { animation: spin 0.9s linear infinite; }
        .fade-in { animation: fadeIn 0.4s ease forwards; }
    </style>
</head>
<body class="bg-gray-50 antialiased font-sans">

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm text-center fade-in">

        {{-- Logo --}}
        <div class="inline-flex items-center justify-center w-14 h-14 bg-white rounded-2xl border border-gray-200 shadow-sm mb-6">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-8 py-10">

            {{-- Spinner --}}
            <div class="flex justify-center mb-6">
                <div class="w-10 h-10 rounded-full border-4 border-gray-200 border-t-blue-600 spin"></div>
            </div>

            <h1 class="text-lg font-semibold text-gray-900 mb-2">Verbindung wird überprüft</h1>
            <p class="text-sm text-gray-500 leading-relaxed">
                Bitte warten Sie einen Moment. Ihr Browser wird verifiziert.
            </p>

            {{-- Progress dots --}}
            <div class="flex justify-center gap-1.5 mt-6" id="progress-dots">
                <div class="w-2 h-2 rounded-full bg-blue-600 opacity-100"></div>
                <div class="w-2 h-2 rounded-full bg-gray-200" id="dot-2"></div>
                <div class="w-2 h-2 rounded-full bg-gray-200" id="dot-3"></div>
            </div>

        </div>

        <p class="text-xs text-gray-400 mt-5">
            Sicherheitscheck durch froxlor WAF
        </p>

    </div>
</div>

{{-- Hidden form — submitted by JS after token computation --}}
<form id="waf-form" method="POST" action="{{ route('waf.verify') }}" style="display:none">
    @csrf
    <input type="hidden" name="token" id="waf-token" value="">
    <input type="hidden" name="return_url" value="{{ $returnUrl }}">
</form>

<script>
(function() {
    'use strict';

    var token = {{ json_encode($token) }};
    var dot2  = document.getElementById('dot-2');
    var dot3  = document.getElementById('dot-3');

    // Simulate a short thinking animation, then submit
    setTimeout(function() {
        if (dot2) dot2.className = 'w-2 h-2 rounded-full bg-blue-600';
    }, 700);

    setTimeout(function() {
        if (dot3) dot3.className = 'w-2 h-2 rounded-full bg-blue-600';
    }, 1400);

    setTimeout(function() {
        var tokenInput = document.getElementById('waf-token');
        if (tokenInput) {
            tokenInput.value = token;
        }
        document.getElementById('waf-form').submit();
    }, 1800);
})();
</script>

</body>
</html>
