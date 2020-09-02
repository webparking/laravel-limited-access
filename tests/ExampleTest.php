<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Tests;

class ExampleTest extends TestCase
{
    public function testCanRun(): void
    {
        $this->assertNotSame(1, 0);
    }
}
