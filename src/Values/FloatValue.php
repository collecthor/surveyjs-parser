<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\NumericValueInterface;

class FloatValue implements NumericValueInterface
{
    public function __construct(private readonly float $rawValue)
    {
    }

    public function getRawValue(): float
    {
        return $this->rawValue;
    }
}
