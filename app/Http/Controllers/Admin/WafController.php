<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WafLog;
use App\Models\WafRule;
use Illuminate\Http\Request;

class WafController extends Controller
{
    public function rules()
    {
        $rules = WafRule::latest()->paginate(50);

        return view('admin.waf.rules', compact('rules'));
    }

    public function createRule()
    {
        return view('admin.waf.create-rule');
    }

    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'type'   => ['required', 'in:ip,cidr,useragent'],
            'value'  => ['required', 'string', 'max:500'],
            'action' => ['required', 'in:block,challenge'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        WafRule::create($data);

        return redirect()->route('admin.waf.rules')
            ->with('success', 'Regel wurde hinzugefügt.');
    }

    public function toggleRule(WafRule $rule)
    {
        $rule->update(['active' => !$rule->active]);

        return back()->with('success', 'Regel wurde ' . ($rule->active ? 'aktiviert' : 'deaktiviert') . '.');
    }

    public function destroyRule(WafRule $rule)
    {
        $rule->delete();

        return redirect()->route('admin.waf.rules')
            ->with('success', 'Regel wurde gelöscht.');
    }

    public function logs(Request $request)
    {
        $logs = WafLog::with('domain')
            ->when($request->filter_action, fn($q) => $q->where('action', $request->filter_action))
            ->when($request->filter_ip, fn($q) => $q->where('ip', 'like', '%' . $request->filter_ip . '%'))
            ->latest('created_at')
            ->paginate(50);

        $stats = [
            'total'      => WafLog::count(),
            'blocked'    => WafLog::where('action', 'blocked')->count(),
            'challenged' => WafLog::where('action', 'challenged')->count(),
            'passed'     => WafLog::where('action', 'passed')->count(),
        ];

        return view('admin.waf.logs', compact('logs', 'stats'));
    }
}
