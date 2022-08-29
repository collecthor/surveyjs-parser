<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\MissingValueInterface;
use Collecthor\DataInterfaces\ValueInterface;

class MissingBooleanValue implements MissingValueInterface, ValueInterface
{
    public function __construct(
        private readonly bool|null $rawValue = null,
        private readonly bool $isSystemMissing = false,
    ) {
    }

    public function isSystemMissing(): bool
    {
        return $this->isSystemMissing;
    }

    public function getRawValue(): bool|null
    {
        return $this->rawValue;
    }
}
