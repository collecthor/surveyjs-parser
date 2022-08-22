<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

trait GetRawConfiguration
{
    /**
     * @var array<string, mixed>
     */
    private array $rawConfiguration;

    public function getRawConfigurationValue(string $key): mixed
    {
        return $this->rawConfiguration[$key] ?? null;
    }
}
