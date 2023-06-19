<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

final class NotNormalValue implements NotNormalValueInterface
{
    /**
     * @param ValueType::Invalid|ValueType::SystemMissing|ValueType::Missing $valueType
     * @param string|int|float|bool|array<mixed>|\DateTimeInterface|null $rawValue
     */
    public function __construct(
        private readonly ValueType $valueType,
        private readonly string|int|float|bool|null|array|\DateTimeInterface $rawValue = null
    ) {
        if ($this->valueType === ValueType::Normal) {
            throw new \InvalidArgumentException();
        }
    }

    public function getValue(): never
    {
        throw new \Exception('Value is missing');
    }

    public function getType(): ValueType
    {
        return $this->valueType;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        if ($this->valueType === ValueType::Missing) {
            return 'missing';
        }
        return match (true) {
            is_array($this->rawValue) => print_r($this->rawValue, true),
            is_string($this->rawValue) => $this->rawValue,
            is_double($this->rawValue) => number_format($this->rawValue, 2),
            is_bool($this->rawValue) => $this->rawValue ? '1' : '0',
            is_null($this->rawValue) => "null",
            is_int($this->rawValue) => (string) $this->rawValue,
            $this->rawValue instanceof \DateTimeInterface => $this->rawValue->format(\DateTimeInterface::RFC3339)
        };
    }

    public function getRawValue(): string|int|float|bool|null|array|\DateTimeInterface
    {
        return $this->rawValue;
    }

    public static function missing(): self
    {
        return new self(ValueType::Missing);
    }

    /**
     * @param string|int|float|bool|array<mixed>|\DateTimeInterface|null $rawValue
     */
    public static function invalid(string|int|float|bool|null|array|\DateTimeInterface $rawValue): self
    {
        return new self(ValueType::Invalid, $rawValue);
    }
}
