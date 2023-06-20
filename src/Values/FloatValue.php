<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\RawValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

class FloatValue implements RawValueInterface
{
    public function __construct(private readonly float $rawValue)
    {
    }

    public function getRawValue(): float
    {
        return $this->rawValue;
    }

    public function getValue(): float
    {
        return $this->rawValue;
    }

    public function getType(): ValueType
    {
        return ValueType::Normal;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return number_format($this->rawValue, 2);
    }
}
