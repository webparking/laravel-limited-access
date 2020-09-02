<?php

namespace Webparking\LimitedAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;

class LimitedAccess
{
    /**
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('limited-access.enabled')) {
            return $next($request);
        }

        if ($this->isIpAddressBlocked($request)) {
            return app(ResponseFactory::class)->view('LimitedAccess::login');
        }

        if ($this->isIpAddressIgnored($request)) {
            return $next($request);
        }

        /** @var Route|null $route */
        $route = $request->route();

        if (null !== $route && !Session::get('limited-access-granted') && 'LimitedAccess::login' !== $route->getName()) {
            return app(ResponseFactory::class)->view('LimitedAccess::login');
        }

        return $next($request);
    }

    private function isIpAddressBlocked(Request $request): bool
    {
        foreach ((array) config('limited-access.block_ips') as $range) {
            if ($request->ip() === $range) {
                return true;
            }

            if ($this->cidrMatch($request->ip(), $range)) {
                return true;
            }
        }

        return false;
    }

    private function isIpAddressIgnored(Request $request): bool
    {
        foreach ((array) config('limited-access.ignore_ips') as $range) {
            if ($request->ip() === $range) {
                return true;
            }

            if ($this->cidrMatch($request->ip(), $range)) {
                return true;
            }
        }

        return false;
    }

    private function cidrMatch(string $requestIp, string $ip): bool
    {
        if (substr_count($requestIp, ':') > 1) {
            return $this->checkIPv6($requestIp, $ip);
        }

        return $this->checkIPv4($requestIp, $ip);
    }

    private function checkIPv4(string $requestIp, $ip): bool
    {
        /** @var string[] $explosion */
        $explosion = explode('/', $ip, 2);

        $address = $explosion[0];
        $netmask = $explosion[1] ?? 32;

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
        /** @var string[] $explosion */
        $explosion = explode('/', $ip, 2);

        $address = $explosion[0];
        $netmask = $explosion[1] ?? 128;

        if ($netmask < 1 || $netmask > 128) {
            return false;
        }

        $addressBytes = unpack('n*', inet_pton($address));
        $requestBytes = unpack('n*', inet_pton($requestIp));

        if (!$addressBytes || !$requestBytes) {
            return false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = min($netmask - 16 * ($i - 1), 16);
            $mask = ~(0xffff >> $left) & 0xffff;
            if ($addressBytes[$i] & $mask !== $requestBytes[$i] & $mask) {
                return false;
            }
        }

        return true;
    }
}
