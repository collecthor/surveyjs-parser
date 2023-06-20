<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\NumericValueInterface;
use Collecthor\SurveyjsParser\Interfaces\RawValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

class IntegerValue implements RawValueInterface
{
    public function __construct(private readonly int $rawValue)
    {
    }

    public function getRawValue(): int
    {
        return $this->rawValue;
    }

    public function getValue(): int
    {
        return $this->rawValue;
    }

    public function getType(): ValueType
    {
        return ValueType::Normal;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return (string) $this->rawValue;
    }
}
