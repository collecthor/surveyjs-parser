<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\NumericValueInterface;

class IntegerValue implements NumericValueInterface
{
    public function __construct(private readonly int $rawValue)
    {
    }

    public function getRawValue(): int
    {
        return $this->rawValue;
    }
}
