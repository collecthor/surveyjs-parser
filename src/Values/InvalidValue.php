<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

final readonly class InvalidValue implements SpecialValueInterface
{
    public function __construct(private mixed $value)
    {
    }

    public function getType(): ValueType
    {
        return ValueType::Invalid;
    }

    public function getValue(): string
    {
        if (
            is_scalar($this->value)
            || is_null($this->value)
            || $this->value instanceof \DateTimeInterface
            || is_array($this->value)
        ) {
            return StringValue::toString($this->value);
        } elseif (is_object($this->value)) {
            return "Value of type " . $this->value::class;
        } else {
            return "Value of unknown type";
        }
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return $this->getValue();
    }
}
