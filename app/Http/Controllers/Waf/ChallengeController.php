<?php

namespace App\Http\Controllers\Waf;

use App\Http\Controllers\Controller;
use App\Services\Waf\ChallengeTokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class ChallengeController extends Controller
{
    public function __construct(private ChallengeTokenService $tokenService) {}

    /**
     * Show the JS challenge page.
     */
    public function show(Request $request)
    {
        $returnUrl = $request->query('return', '/');
        $token     = $this->tokenService->generate($request);

        return view('waf.challenge', compact('returnUrl', 'token'));
    }

    /**
     * Verify the JS challenge and set the pass cookie.
     */
    public function verify(Request $request)
    {
        $token     = $request->input('token', '');
        $returnUrl = $request->input('return_url', '/');

        if (!$this->tokenService->verify($request, $token)) {
            return view('waf.blocked', ['reason' => 'challenge_failed']);
        }

        $cookieName     = config('waf.cookie_name', 'frx_waf_pass');
        $cookieValue    = $this->tokenService->generateCookie($request);
        $cookieLifetime = (int) config('waf.cookie_lifetime', 240);

        $cookie = Cookie::create($cookieName)
            ->withValue($cookieValue)
            ->withExpires(time() + $cookieLifetime * 60)
            ->withPath('/')
            ->withHttpOnly(true)
            ->withSameSite('Lax');

        return redirect()->away($returnUrl)->withCookie($cookie);
    }
}
