<?php

namespace App\Services\Waf;

use Illuminate\Http\Request;

class ChallengeTokenService
{
    private string $secret;
    private int    $window;

    public function __construct()
    {
        $this->secret = config('app.key');
        $this->window = (int) config('waf.token_window', 300);
    }

    /**
     * Generate a signed challenge token for the given request.
     * Token is bound to IP, a UA hash, and the current time bucket.
     */
    public function generate(Request $request): string
    {
        $bucket  = $this->timeBucket();
        $payload = $request->ip() . ':' . $this->uaHash($request) . ':' . $bucket;

        return base64_encode(hash_hmac('sha256', $payload, $this->secret, true));
    }

    /**
     * Verify a challenge token. Accept the current bucket and the previous one
     * to allow for token generation at the edge of a window.
     */
    public function verify(Request $request, string $token): bool
    {
        $decoded = base64_decode($token, strict: true);
        if ($decoded === false) {
            return false;
        }

        foreach ([$this->timeBucket(), $this->timeBucket() - 1] as $bucket) {
            $payload  = $request->ip() . ':' . $this->uaHash($request) . ':' . $bucket;
            $expected = hash_hmac('sha256', $payload, $this->secret, true);

            if (hash_equals($expected, $decoded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a signed pass cookie value.
     */
    public function generateCookie(Request $request): string
    {
        $expires = time() + (int) config('waf.cookie_lifetime', 240) * 60;
        $payload = $request->ip() . ':' . $expires;

        return base64_encode($expires . ':' . hash_hmac('sha256', $payload, $this->secret));
    }

    /**
     * Verify a pass cookie value.
     */
    public function verifyCookie(Request $request, string $cookie): bool
    {
        $decoded = base64_decode($cookie, strict: true);
        if ($decoded === false) {
            return false;
        }

        $parts = explode(':', $decoded, 2);
        if (count($parts) !== 2) {
            return false;
        }

        [$expires, $signature] = $parts;

        if ((int) $expires < time()) {
            return false;
        }

        $payload  = $request->ip() . ':' . $expires;
        $expected = hash_hmac('sha256', $payload, $this->secret);

        return hash_equals($expected, $signature);
    }

    private function timeBucket(): int
    {
        return (int) floor(time() / $this->window);
    }

    private function uaHash(Request $request): string
    {
        return substr(hash('sha256', (string) $request->userAgent()), 0, 8);
    }
}
