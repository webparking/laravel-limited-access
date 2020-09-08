<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Ip;

use Symfony\Component\HttpFoundation\IpUtils;

class IpAddressChecker
{
    public function isBlocked(string $ip): bool
    {
        return IpUtils::checkIp($ip, (array) config('limited-access.block_ips', []));
    }

    public function isIgnored(string $ip): bool
    {
        return IpUtils::checkIp($ip, (array) config('limited-access.ignore_ips', []));
    }
}
