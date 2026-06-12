<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zugriff verweigert</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 antialiased font-sans">

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm text-center">

        {{-- Icon --}}
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 rounded-2xl border border-red-200 mb-6">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-8 py-10">

            <h1 class="text-xl font-semibold text-gray-900 mb-3">Zugriff verweigert</h1>
            <p class="text-sm text-gray-500 leading-relaxed mb-6">
                Ihr Zugriff auf diese Website wurde durch die Web Application Firewall blockiert.
            </p>

            <div class="bg-gray-50 rounded-xl p-4 text-left space-y-2 mb-6">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-400">Status</span>
                    <span class="font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">403 Forbidden</span>
                </div>
                @if(isset($reason))
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-400">Grund</span>
                    <span class="font-mono text-gray-600">{{ $reason }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-400">Zeitstempel</span>
                    <span class="font-mono text-gray-600">{{ now()->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            <p class="text-xs text-gray-400">
                Wenn Sie glauben, dass dies ein Fehler ist, kontaktieren Sie den Administrator dieser Website.
            </p>

        </div>

        <p class="text-xs text-gray-400 mt-5">
            Sicherheitscheck durch froxlor WAF
        </p>

    </div>
</div>

</body>
</html>
