<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess;

use Illuminate\Config\Repository as ConfigRepository;

final class Codes
{
    /**
     * @var ConfigRepository
     */
    private $config;

    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;
    }

    /**
     * @return string[]
     */
    public function get(): array
    {
        $codes = $this->config->get('limited-access.codes');

        return array_map(static fn (string $code): string => trim($code), explode(',', $codes));
    }
}
