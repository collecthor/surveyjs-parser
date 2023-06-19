<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\RawValueInterface;
use Collecthor\SurveyjsParser\Interfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

class StringValue implements StringValueInterface
{
    final public function __construct(private readonly string $rawValue)
    {
    }

    /**
     * @param array<mixed>|float|bool|int|string|null $rawValue
     * @return static
     */
    public static function fromRawValue(array|float|bool|int|string|null|\DateTimeInterface $rawValue): static
    {
        return match (true) {
            is_array($rawValue) => new static(print_r($rawValue, true)),
            is_string($rawValue) => new static($rawValue),
            is_double($rawValue) => new static(number_format($rawValue, 2)),
            is_bool($rawValue) => new static($rawValue ? '1' : '0'),
            is_null($rawValue) => new static("null"),
            is_int($rawValue) => new static((string) $rawValue),
            $rawValue instanceof \DateTimeInterface => new static($rawValue->format(\DateTimeInterface::RFC3339))
        };
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getValue(): string
    {
        return $this->rawValue;
    }

    public function getType(): ValueType
    {
        return ValueType::Normal;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return $this->rawValue;
    }
}
