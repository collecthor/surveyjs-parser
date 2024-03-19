<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\BaseValueInterface;

readonly class FloatValue implements BaseValueInterface
{
    public function __construct(private float $rawValue)
    {
    }

    public function getValue(): float
    {
        return $this->rawValue;
    }
    public function getDisplayValue(?string $locale = null): string
    {
        return number_format($this->rawValue, 2);
    }
}
