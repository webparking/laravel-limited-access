<?php

namespace Webparking\LimitedAccess\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Webparking\LimitedAccess\Http\Requests\LimitedAccessRequest;
use Webparking\LimitedAccess\Ip\IpAddressChecker;

class LimitedAccessController extends Controller
{
    /**
     * @var IpAddressChecker
     */
    private $ipAddressChecker;

    /**
     * @var Redirector
     */
    private $redirector;

    public function __construct(IpAddressChecker $ipAddressChecker, Redirector $redirector)
    {
        $this->ipAddressChecker = $ipAddressChecker;
        $this->redirector = $redirector;
    }

    public function validate(LimitedAccessRequest $request): RedirectResponse
    {
        if (!$this->ipAddressChecker->isBlocked((string) $request->ip())) {
            session()->put('limited-access-granted', true);
        }

        return $this->redirector->intended('/');
    }
}
