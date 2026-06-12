<?php

namespace App\Services\Waf;

use App\Models\Domain;
use App\Models\WafLog;
use Illuminate\Http\Request;

class WafService
{
    public function __construct(
        private IpChecker            $ipChecker,
        private UserAgentChecker     $uaChecker,
        private ChallengeTokenService $tokenService,
    ) {}

    /**
     * Evaluate a request against WAF rules.
     *
     * Returns one of: 'allow', 'challenge', 'block'
     */
    public function evaluate(Request $request, ?Domain $domain = null): string
    {
        $cookieName = config('waf.cookie_name', 'frx_waf_pass');

        // 1. Valid pass cookie → allow immediately
        $cookie = $request->cookie($cookieName);
        if ($cookie && $this->tokenService->verifyCookie($request, $cookie)) {
            return 'allow';
        }

        // 2. IP / CIDR check
        $ipAction = $this->ipChecker->check($request->ip());
        if ($ipAction !== null) {
            $this->log($request, $domain, $ipAction === 'block' ? 'blocked' : 'challenged', 'ip_rule');

            return $ipAction;
        }

        // 3. User-Agent check
        $uaAction = $this->uaChecker->check($request->userAgent());
        if ($uaAction !== null) {
            $this->log($request, $domain, $uaAction === 'block' ? 'blocked' : 'challenged', 'user_agent');

            return $uaAction;
        }

        // 4. New visitor → challenge
        $this->log($request, $domain, 'challenged', 'new_visitor');

        return 'challenge';
    }

    private function log(Request $request, ?Domain $domain, string $action, string $reason): void
    {
        if (!config('waf.logging', true)) {
            return;
        }

        WafLog::create([
            'domain_id'  => $domain?->id,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action'     => $action,
            'reason'     => $reason,
            'url'        => $request->url(),
        ]);
    }
}
