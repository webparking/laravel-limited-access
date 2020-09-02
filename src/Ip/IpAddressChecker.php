<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Ip;

class IpAddressChecker
{
    public function isBlocked(string $ip): bool
    {
        foreach ((array) config('limited-access.block_ips', []) as $range) {
            if ($ip === $range) {
                return true;
            }

            if ($this->doesCidrMatch($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    public function isIgnored(string $ip): bool
    {
        foreach ((array) config('limited-access.ignore_ips', []) as $range) {
            if ($ip === $range) {
                return true;
            }

            if ($this->doesCidrMatch($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    private function doesCidrMatch(string $requestIp, string $ip): bool
    {
        if (substr_count($requestIp, ':') > 1) {
            return $this->checkIPv6($requestIp, $ip);
        }

        return $this->checkIPv4($requestIp, $ip);
    }

    private function checkIPv4(string $requestIp, string $ip): bool
    {
        /** @var string[] $cidrParts */
        $cidrParts = explode('/', $ip, 2);

        $address = $cidrParts[0];
        $netmask = (int) ($cidrParts[1] ?? 32);

        if ($netmask < 0 || $netmask > 32) {
            return false;
        }

        return 0 === substr_compare(
            sprintf('%032b', ip2long($requestIp)),
            sprintf('%032b', ip2long($address)),
            0,
            $netmask
        );
    }

    private function checkIPv6(string $requestIp, string $ip): bool
    {
        /** @var string[] $cidrParts */
        $cidrParts = explode('/', $ip, 2);

        $address = $cidrParts[0];
        $netmask = (int) ($cidrParts[1] ?? 128);

        if ($netmask < 1 || $netmask > 128) {
            return false;
        }

        $packedAddress = inet_pton($address);
        $packedRequestIp = inet_pton($requestIp);

        if (false === $packedAddress || false === $packedRequestIp) {
            return false;
        }

        $addressBytes = unpack('n*', $packedAddress);
        $requestBytes = unpack('n*', $packedRequestIp);

        if (!$addressBytes || !$requestBytes) {
            return false;
        }

        /*
         * Taken from symfony/http-foundation
         *
         * @link https://github.com/symfony/http-foundation/blob/master/IpUtils.php#L142
         */
        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xffff >> $left) & 0xffff;
            if (($addressBytes[$i] & $mask) !== ($requestBytes[$i] & $mask)) {
                return false;
            }
        }

        return true;
    }
}
