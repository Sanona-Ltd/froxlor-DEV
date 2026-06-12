<?php

namespace App\Http\Middleware;

use App\Services\Waf\IpChecker;
use App\Services\Waf\UserAgentChecker;
use App\Services\Waf\WafService;
use App\Services\Waf\ChallengeTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WafMiddleware
{
    public function __construct(
        private WafService            $wafService,
        private ChallengeTokenService $tokenService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Skip WAF for bypass paths (admin, panel, login, etc.)
        foreach (config('waf.bypass_paths', []) as $path) {
            if (str_starts_with($request->getPathInfo(), $path)) {
                return $next($request);
            }
        }

        $result = $this->wafService->evaluate($request);

        return match($result) {
            'block'     => response()->view('waf.blocked', ['reason' => 'access_denied'], 403),
            'challenge' => redirect()->route('waf.challenge', [
                'return' => $request->fullUrl(),
                'token'  => $this->tokenService->generate($request),
            ]),
            default     => $next($request),
        };
    }
}
