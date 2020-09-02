<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Exceptions;

use Throwable;

class CodesNotSetException extends \RuntimeException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('You must set at least 1 code in limited-access.codes', $code, $previous);
    }

}