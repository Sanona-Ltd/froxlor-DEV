{{--
    Shared form fields for domain create/edit.
    Props: $webservers, $domain (optional for edit), $customers (optional for admin), $isAdmin
--}}
@props(['webservers', 'domain' => null, 'customers' => null, 'isAdmin' => false])

<div class="space-y-6">

    @if($isAdmin && $customers)
    <div>
        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">Kunde</label>
        <select id="user_id" name="user_id"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
            <option value="">— Kunde auswählen —</option>
            @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(old('user_id', $domain?->user_id) == $customer->id)>
                {{ $customer->name }} ({{ $customer->email }})
            </option>
            @endforeach
        </select>
        @error('user_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    @endif

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Domain-Name</label>
        <input id="name" type="text" name="name"
               value="{{ old('name', $domain?->name) }}"
               placeholder="example.com"
               {{ $domain ? 'readonly' : '' }}
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition {{ $domain ? 'bg-gray-50 cursor-not-allowed' : '' }}">
        @if($domain)<p class="mt-1 text-xs text-gray-400">Der Domain-Name kann nicht geändert werden.</p>@endif
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="document_root" class="block text-sm font-medium text-gray-700 mb-1.5">Document Root</label>
        <input id="document_root" type="text" name="document_root"
               value="{{ old('document_root', $domain?->document_root ?? '/var/www/{domain}/public') }}"
               placeholder="/var/www/{domain}/public"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono">
        <p class="mt-1 text-xs text-gray-400">Verwende <code class="bg-gray-100 px-1 rounded">{domain}</code> als Platzhalter für den Domain-Namen.</p>
        @error('document_root')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="webserver" class="block text-sm font-medium text-gray-700 mb-1.5">Webserver</label>
        <select id="webserver" name="webserver"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
            @foreach($webservers as $ws)
            <option value="{{ $ws['value'] }}" @selected(old('webserver', $domain?->webserver?->value ?? 'apache') === $ws['value'])>
                {{ $ws['label'] }}
            </option>
            @endforeach
        </select>
        @error('webserver')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="php_version" class="block text-sm font-medium text-gray-700 mb-1.5">
            PHP Version <span class="text-gray-400 font-normal">(optional)</span>
        </label>
        <input id="php_version" type="text" name="php_version"
               value="{{ old('php_version', $domain?->php_version) }}"
               placeholder="8.3"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        @error('php_version')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl hover:border-blue-200 transition-colors">
            <div class="pt-0.5">
                <input id="ssl_enabled" type="checkbox" name="ssl_enabled" value="1"
                       @checked(old('ssl_enabled', $domain?->ssl_enabled))
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </div>
            <div>
                <label for="ssl_enabled" class="text-sm font-medium text-gray-900 cursor-pointer">SSL / HTTPS</label>
                <p class="text-xs text-gray-500 mt-0.5">Let's Encrypt Zertifikat aktivieren</p>
            </div>
        </div>

        @if($isAdmin)
        <div class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl hover:border-blue-200 transition-colors">
            <div class="pt-0.5">
                <input id="waf_enabled" type="checkbox" name="waf_enabled" value="1"
                       @checked(old('waf_enabled', $domain?->waf_enabled))
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </div>
            <div>
                <label for="waf_enabled" class="text-sm font-medium text-gray-900 cursor-pointer">WAF aktivieren</label>
                <p class="text-xs text-gray-500 mt-0.5">Firewall & JS-Challenge für diese Domain</p>
            </div>
        </div>
        @endif
    </div>

</div>
