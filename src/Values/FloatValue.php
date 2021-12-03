<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\NumericValueInterface;
use Collecthor\DataInterfaces\StringValueInterface;

class FloatValue implements NumericValueInterface
{
    public function __construct(private float $rawValue)
    {
    }

    public function getRawValue(): float
    {
        return $this->rawValue;
    }
}
