<?php

namespace App\Services\Waf;

use App\Models\WafRule;
use Illuminate\Support\Collection;

class UserAgentChecker
{
    private Collection $rules;

    private static array $defaultBadPatterns = [
        'sqlmap',
        'nikto',
        'nmap',
        'masscan',
        'zgrab',
        'nuclei',
        'dirbuster',
        'hydra',
        'curl/7.1',
        'python-requests/1',
        'go-http-client/1',
        'scrapy',
        'wget/',
        'libwww-perl',
    ];

    public function __construct()
    {
        $this->rules = WafRule::active()
            ->where('type', 'useragent')
            ->get();
    }

    public function check(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '') {
            return 'block';
        }

        $ua = strtolower($userAgent);

        // Check custom DB rules first
        foreach ($this->rules as $rule) {
            if (str_contains($ua, strtolower($rule->value))) {
                return $rule->action;
            }
        }

        // Check built-in bad patterns
        foreach (self::$defaultBadPatterns as $pattern) {
            if (str_contains($ua, $pattern)) {
                return 'block';
            }
        }

        return null;
    }
}
