<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\StringValueInterface;

readonly class StringValue implements StringValueInterface
{
    private string $value;

    /**
     * @phpstan-param array<mixed>|float|bool|int|string|\DateTimeInterface|null $rawValue
     */
    final public function __construct(private array|float|bool|int|string|null|\DateTimeInterface $rawValue)
    {
        $this->value = self::toString($this->rawValue);
    }

    /**
     * @param array<mixed>|float|bool|int|string|null $rawValue
     */
    public static function toString(array|float|bool|int|string|null|\DateTimeInterface $rawValue): string
    {
        return match (true) {
            is_array($rawValue) => print_r($rawValue, true),
            is_string($rawValue) => $rawValue,
            is_double($rawValue) => number_format($rawValue, 2),
            is_bool($rawValue) => $rawValue ? '1' : '0',
            is_null($rawValue) => "null",
            is_int($rawValue) => (string) $rawValue,
            $rawValue instanceof \DateTimeInterface => $rawValue->format(\DateTimeInterface::RFC3339)
        };
    }

    /**
     * @phpstan-return array<mixed>|float|bool|int|string|\DateTimeInterface|null
     */
    public function getRawValue(): array|float|bool|int|string|null|\DateTimeInterface
    {
        return $this->rawValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return $this->value;
    }
}
