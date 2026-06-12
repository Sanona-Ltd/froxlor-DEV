<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Webserver;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::where('user_id', Auth::id())->latest()->paginate(25);

        return view('panel.domains.index', compact('domains'));
    }

    public function create()
    {
        $webservers = Webserver::options();

        return view('panel.domains.create', compact('webservers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:domains,name'],
            'document_root' => ['required', 'string', 'max:500'],
            'webserver'     => ['required', 'in:apache,nginx,openlitespeed'],
            'php_version'   => ['nullable', 'string', 'max:10'],
            'ssl_enabled'   => ['boolean'],
        ]);

        $data['user_id']     = Auth::id();
        $data['ssl_enabled'] = $request->boolean('ssl_enabled');
        $data['waf_enabled'] = false;

        Domain::create($data);

        return redirect()->route('panel.domains.index')
            ->with('success', "Domain {$data['name']} wurde erfolgreich hinzugefügt.");
    }

    public function edit(Domain $domain)
    {
        $this->authorizeOwnership($domain);
        $webservers = Webserver::options();

        return view('panel.domains.edit', compact('domain', 'webservers'));
    }

    public function update(Request $request, Domain $domain)
    {
        $this->authorizeOwnership($domain);

        $data = $request->validate([
            'document_root' => ['required', 'string', 'max:500'],
            'webserver'     => ['required', 'in:apache,nginx,openlitespeed'],
            'php_version'   => ['nullable', 'string', 'max:10'],
            'ssl_enabled'   => ['boolean'],
        ]);

        $data['ssl_enabled'] = $request->boolean('ssl_enabled');

        $domain->update($data);

        return redirect()->route('panel.domains.index')
            ->with('success', "Domain {$domain->name} wurde aktualisiert.");
    }

    public function destroy(Domain $domain)
    {
        $this->authorizeOwnership($domain);
        $name = $domain->name;
        $domain->delete();

        return redirect()->route('panel.domains.index')
            ->with('success', "Domain {$name} wurde gelöscht.");
    }

    private function authorizeOwnership(Domain $domain): void
    {
        if ($domain->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
