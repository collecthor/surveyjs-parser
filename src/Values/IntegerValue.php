<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\NumericValueInterface;
use Collecthor\DataInterfaces\StringValueInterface;

class IntegerValue implements NumericValueInterface
{
    public function __construct(private int $rawValue)
    {
    }

    public function getRawValue(): int
    {
        return $this->rawValue;
    }
}
