<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\MissingValueInterface;
use Collecthor\DataInterfaces\StringValueInterface;

class MissingStringValue implements MissingValueInterface, StringValueInterface
{
    public function __construct(
        private readonly string $rawValue = '',
        private readonly bool $isSystemMissing = true
    ) {
    }


    public function isSystemMissing(): bool
    {
        return $this->isSystemMissing;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }
}
