<?php

namespace Webparking\LimitedAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;
use Webparking\LimitedAccess\Exceptions\CodesNotSetException;
use Webparking\LimitedAccess\Ip\IpAddressChecker;

class LimitedAccess
{
    /**
     * @var IpAddressChecker
     */
    private $ipAddressChecker;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct(IpAddressChecker $ipAddressChecker, ResponseFactory $responseFactory)
    {
        $this->ipAddressChecker = $ipAddressChecker;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->areCodesSet()) {
            throw new CodesNotSetException();
        }

        if (!config('limited-access.enabled')) {
            return $next($request);
        }

        if ($this->ipAddressChecker->isBlocked($request)) {
            return $this->responseFactory->view('LimitedAccess::login');
        }

        if ($this->ipAddressChecker->isIgnored($request)) {
            return $next($request);
        }

        /** @var Route|null $route */
        $route = $request->route();

        if ($route && 'LimitedAccess::login' !== $route->getName() && !$request->session()->get('limited-access-granted')) {
            return $this->responseFactory->view('LimitedAccess::login');
        }

        return $next($request);
    }

    private function areCodesSet(): bool
    {
        if (config('limited-access.enabled')) {
            $codes = explode(',', config('limited-access.codes', ''));

            return count($codes) > 0;
        }

        return true;
    }
}
