<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\MissingValueInterface;
use Collecthor\DataInterfaces\NumericValueInterface;

class MissingIntegerValue implements MissingValueInterface, NumericValueInterface
{
    public function __construct(
        private int $rawValue,
        private bool $isSystemMissing = true
    ) {
    }


    public function isSystemMissing(): bool
    {
        return $this->isSystemMissing;
    }

    public function getRawValue(): int
    {
        return $this->rawValue;
    }
}
