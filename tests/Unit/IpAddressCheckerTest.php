<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Tests\Unit;

use Webparking\LimitedAccess\Ip\IpAddressChecker;
use Webparking\LimitedAccess\Tests\TestCase;

class IpAddressCheckerTest extends TestCase
{
    /**
     * @var IpAddressChecker
     */
    private $ipAddressChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->ipAddressChecker = $this->app->make(IpAddressChecker::class);
    }

    /**
     * @dataProvider validAddressProvider
     */
    public function testValidAddressesWithNoBlockOrIgnore(string $ipAddress): void
    {
        $this->assertFalse($this->ipAddressChecker->isIgnored($ipAddress));
        $this->assertFalse($this->ipAddressChecker->isBlocked($ipAddress));
    }

    public function testBlockedAddresses(): void
    {
        $this->assertTrue($this->ipAddressChecker->isBlocked('192.168.2.1'));
        $this->assertTrue($this->ipAddressChecker->isBlocked('192.168.2.50'));
        $this->assertTrue($this->ipAddressChecker->isBlocked('192.168.2.200'));
        $this->assertTrue($this->ipAddressChecker->isBlocked('2607:f0d0:1002:52::5'));
        $this->assertTrue($this->ipAddressChecker->isBlocked('2607:f0d0:1002:52::ffff'));
        $this->assertTrue($this->ipAddressChecker->isBlocked('2607:f0d0:1002:52::c0de'));

        $this->assertFalse($this->ipAddressChecker->isBlocked('192.168.1.1'));
        $this->assertFalse($this->ipAddressChecker->isBlocked('127.0.0.1'));
        $this->assertFalse($this->ipAddressChecker->isBlocked('192.168.3.1'));
        $this->assertFalse($this->ipAddressChecker->isBlocked('2607:f0d0:1002:51:1::4'));
        $this->assertFalse($this->ipAddressChecker->isBlocked('2607:f0d0:1002:ff:1'));
    }

    public function testIgnoredAddresses(): void
    {
        $this->assertTrue($this->ipAddressChecker->isIgnored('192.168.1.1'));
        $this->assertTrue($this->ipAddressChecker->isIgnored('192.168.1.50'));
        $this->assertTrue($this->ipAddressChecker->isIgnored('192.168.1.200'));
        $this->assertTrue($this->ipAddressChecker->isIgnored('2607:f0d0:1002:51::5'));
        $this->assertTrue($this->ipAddressChecker->isIgnored('2607:f0d0:1002:51::ffff'));
        $this->assertTrue($this->ipAddressChecker->isIgnored('2607:f0d0:1002:51::c0de'));

        $this->assertFalse($this->ipAddressChecker->isIgnored('192.168.2.1'));
        $this->assertFalse($this->ipAddressChecker->isIgnored('127.0.0.1'));
        $this->assertFalse($this->ipAddressChecker->isIgnored('192.168.3.1'));
        $this->assertFalse($this->ipAddressChecker->isIgnored('2607:f0d0:1002:51:1::4'));
        $this->assertFalse($this->ipAddressChecker->isIgnored('2607:f0d0:1002:ff:1'));
    }

    /**
     * @return string[][]
     */
    public function validAddressProvider(): array
    {
        return [
            ['127.0.0.1'],
            ['250.3.9.4/32'],
            ['233.21.85.0/28'],
            ['55.66.77.88/31'],
            ['192.168.0.0/16'],
            ['::1'],
            ['2607:f0d0:1002:d2f2::4'],
            ['2607:f0d0:1002:d2f2::4/64'],
        ];
    }
}
