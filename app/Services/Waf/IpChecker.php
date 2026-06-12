<?php

namespace App\Services\Waf;

use App\Models\WafRule;
use Illuminate\Support\Collection;

class IpChecker
{
    private Collection $rules;

    public function __construct()
    {
        $this->rules = WafRule::active()
            ->whereIn('type', ['ip', 'cidr'])
            ->get();
    }

    public function check(string $ip): ?string
    {
        foreach ($this->rules as $rule) {
            $matched = match($rule->type) {
                'ip'   => $this->matchesIp($ip, $rule->value),
                'cidr' => $this->matchesCidr($ip, $rule->value),
                default => false,
            };

            if ($matched) {
                return $rule->action;
            }
        }

        return null;
    }

    private function matchesIp(string $ip, string $ruleValue): bool
    {
        return $ip === $ruleValue;
    }

    private function matchesCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $bits] = explode('/', $cidr, 2);
        $bits = (int) $bits;

        // IPv6 CIDR check
        if (str_contains($ip, ':')) {
            return $this->ipv6InCidr($ip, $subnet, $bits);
        }

        // IPv4 CIDR check
        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = $bits > 0 ? ~((1 << (32 - $bits)) - 1) : 0;

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    private function ipv6InCidr(string $ip, string $subnet, int $bits): bool
    {
        $ipBin     = inet_pton($ip);
        $subnetBin = inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false) {
            return false;
        }

        $byteLen   = (int) ceil($bits / 8);
        $remainder = $bits % 8;

        for ($i = 0; $i < $byteLen - 1; $i++) {
            if ($ipBin[$i] !== $subnetBin[$i]) {
                return false;
            }
        }

        if ($remainder > 0 && $byteLen > 0) {
            $mask = 0xFF & (0xFF << (8 - $remainder));
            if ((ord($ipBin[$byteLen - 1]) & $mask) !== (ord($subnetBin[$byteLen - 1]) & $mask)) {
                return false;
            }
        }

        return true;
    }
}
