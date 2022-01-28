<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Route;
use Webparking\LimitedAccess\Codes;
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

    /**
     * @var Codes
     */
    private $codes;

    /**
     * @var string[]
     */
    private $except = [
        'LimitedAccess::login',
        'LimitedAccess::verify',
    ];

    public function __construct(IpAddressChecker $ipAddressChecker, ResponseFactory $responseFactory, Codes $codes)
    {
        $this->ipAddressChecker = $ipAddressChecker;
        $this->responseFactory = $responseFactory;
        $this->codes = $codes;
    }

    /**
     * @return mixed|Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->areCodesSet()) {
            throw new CodesNotSetException();
        }

        if (!config('limited-access.enabled')) {
            return $next($request);
        }

        if ($this->ipAddressChecker->isBlocked((string) $request->ip())) {
            return $this->responseFactory->view('LimitedAccess::login');
        }

        if ($this->ipAddressChecker->isIgnored((string) $request->ip())) {
            return $next($request);
        }

        /** @var null|Route $route */
        $route = $request->route();

        if (!$request->session()->get('limited-access-granted') && $route && !$route->named($this->except)) {
            return $this->responseFactory->view('LimitedAccess::login');
        }

        return $next($request);
    }

    private function areCodesSet(): bool
    {
        if (config('limited-access.enabled', false)) {
            $codes = $this->codes->get();

            return \count(array_filter($codes)) > 0;
        }

        return true;
    }
}
