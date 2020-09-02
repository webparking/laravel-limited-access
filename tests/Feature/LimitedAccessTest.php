<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Tests\Feature;

use Webparking\LimitedAccess\Tests\TestCase;

class LimitedAccessTest extends TestCase
{
    public function testEnabledNoCodesSet(): void
    {
        $originalCodes = config('limited-access.codes');
        config()->set('limited-access.codes', '');

        $this
            ->get('/secret-stuff')
            ->assertStatus(500);

        config()->set('limited-access.codes', $originalCodes);
    }

    /**
     * @dataProvider validIpsProvider
     */
    public function testValidIp(string $ip): void
    {
        $this->serverVariables['REMOTE_ADDR'] = $ip;

        $this->get('/secret-stuff')
            ->assertStatus(200)
            ->assertSessionMissing('limited-access-granted')
            ->assertSee('Limited access');
    }

    /**
     * @dataProvider validIpsProvider
     */
    public function testValidIpWrongCode(string $ip): void
    {
        $this->serverVariables['REMOTE_ADDR'] = $ip;

        $this
            ->post('/limited-access-login', [
                'code' => 'letyoudown',
            ])
            ->assertSessionMissing('limited-access-granted');
    }

    /**
     * @dataProvider validIpsProvider
     */
    public function testValidIpCorrectCode(string $ip): void
    {
        $this->serverVariables['REMOTE_ADDR'] = $ip;

        session()->put('url.intended', '/secret-stuff');

        $this
            ->post('/limited-access-login', [
                'code' => 'never',
            ])
            ->assertSessionHas('limited-access-granted')
            ->assertRedirect('/secret-stuff');
    }

    /**
     * @dataProvider ignoredIpsProvider
     */
    public function testIgnoredIp(string $ip): void
    {
        $this->serverVariables['REMOTE_ADDR'] = $ip;

        $this
            ->get('/secret-stuff', [
                'code' => 'never',
            ])
            ->assertSessionMissing('limited-access-granted')
            ->assertSee('This is very secret!');
    }

    /**
     * @dataProvider blockedIpsProvider
     */
    public function testBlockedIp(string $ip): void
    {
        $this->serverVariables['REMOTE_ADDR'] = $ip;

        $this
            ->post('/limited-access-login', [
                'code' => 'gonna',
            ])
            ->assertSessionMissing('limited-access-granted');
    }

    public function testBlockedOverridesIgnore(): void
    {
        $cidr = '192.168.1.0/24';
        $ip = '192.168.1.25';

        config()->set('limited-access.block_ips', [$cidr]);
        config()->set('limited-access.ignore_ips', [$cidr]);

        $this->serverVariables['REMOTE_ADDR'] = $ip;

        $this
            ->post('/limited-access-login', [
                'code' => 'give',
            ])
            ->assertSessionMissing('limited-access-granted');
    }

    /**
     * @return string[][]
     */
    public function ignoredIpsProvider(): array
    {
        return [
            ['192.168.1.1'],
            ['2607:f0d0:1002:51::5'],
        ];
    }

    /**
     * @return string[][]
     */
    public function blockedIpsProvider(): array
    {
        return [
            ['192.168.2.1'],
            ['2607:f0d0:1002:52::5'],
        ];
    }

    /**
     * @return string[][]
     */
    public function validIpsProvider(): array
    {
        return [
            ['192.168.3.1'],
            ['2607:f0d0:1002:53::5'],
        ];
    }
}
