<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\InvalidValueInterface;
use Collecthor\DataInterfaces\StringValueInterface;

class InvalidValue implements InvalidValueInterface, StringValueInterface
{
    public function __construct(
        private mixed $rawValue
    ) {
    }

    public function getRawValue(): string
    {
        return is_scalar($this->rawValue) ? (string) $this->rawValue : print_r($this->rawValue, true);
    }

    public function isSystemMissing(): bool
    {
        return true;
    }
}
