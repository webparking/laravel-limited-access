<?php

namespace Webparking\LimitedAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Webparking\LimitedAccess\Ip\IpAddressChecker;

class LimitedAccess
{
    /**
     * @var IpAddressChecker
     */
    private $ipAddressChecker;

    public function __construct(IpAddressChecker $ipAddressChecker)
    {
        $this->ipAddressChecker = $ipAddressChecker;
    }

    /**
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('limited-access.enabled')) {
            return $next($request);
        }

        if ($this->ipAddressChecker->isBlocked($request)) {
            return app(ResponseFactory::class)->view('LimitedAccess::login');
        }

        if ($this->ipAddressChecker->isIgnored($request)) {
            return $next($request);
        }

        /** @var Route|null $route */
        $route = $request->route();

        if (null !== $route && !Session::get('limited-access-granted') && 'LimitedAccess::login' !== $route->getName()) {
            return app(ResponseFactory::class)->view('LimitedAccess::login');
        }

        return $next($request);
    }
}
