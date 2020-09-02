<?php

namespace Webparking\LimitedAccess\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Webparking\LimitedAccess\Http\Requests\LimitedAccessRequest;

class LimitedAccessController extends Controller
{
    public function validate(LimitedAccessRequest $request): RedirectResponse
    {
        session()->put('limited-access-granted', true);

        /** @var Redirector $redirector */
        $redirector = app('redirect');

        return $redirector->to(session('url.intended', '/'));
    }
}
