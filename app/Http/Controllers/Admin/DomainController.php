<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Webserver;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::with('user')->latest()->paginate(25);

        return view('admin.domains.index', compact('domains'));
    }

    public function create()
    {
        $customers = User::where('is_admin', false)->orderBy('name')->get();
        $webservers = Webserver::options();

        return view('admin.domains.create', compact('customers', 'webservers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'name'          => ['required', 'string', 'max:255', 'unique:domains,name'],
            'document_root' => ['required', 'string', 'max:500'],
            'webserver'     => ['required', 'in:apache,nginx,openlitespeed'],
            'php_version'   => ['nullable', 'string', 'max:10'],
            'ssl_enabled'   => ['boolean'],
            'waf_enabled'   => ['boolean'],
        ]);

        $data['ssl_enabled'] = $request->boolean('ssl_enabled');
        $data['waf_enabled'] = $request->boolean('waf_enabled');

        Domain::create($data);

        return redirect()->route('admin.domains.index')
            ->with('success', "Domain {$data['name']} wurde erfolgreich erstellt.");
    }

    public function edit(Domain $domain)
    {
        $customers = User::where('is_admin', false)->orderBy('name')->get();
        $webservers = Webserver::options();

        return view('admin.domains.edit', compact('domain', 'customers', 'webservers'));
    }

    public function update(Request $request, Domain $domain)
    {
        $data = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'name'          => ['required', 'string', 'max:255', 'unique:domains,name,' . $domain->id],
            'document_root' => ['required', 'string', 'max:500'],
            'webserver'     => ['required', 'in:apache,nginx,openlitespeed'],
            'php_version'   => ['nullable', 'string', 'max:10'],
            'ssl_enabled'   => ['boolean'],
            'waf_enabled'   => ['boolean'],
        ]);

        $data['ssl_enabled'] = $request->boolean('ssl_enabled');
        $data['waf_enabled'] = $request->boolean('waf_enabled');

        $domain->update($data);

        return redirect()->route('admin.domains.index')
            ->with('success', "Domain {$domain->name} wurde aktualisiert.");
    }

    public function destroy(Domain $domain)
    {
        $name = $domain->name;
        $domain->delete();

        return redirect()->route('admin.domains.index')
            ->with('success', "Domain {$name} wurde gelöscht.");
    }
}
