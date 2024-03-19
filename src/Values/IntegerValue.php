<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\IntegerValueInterface;

final class IntegerValue implements IntegerValueInterface
{
    /**
     * @var array<self>
     */
    private static array $cached = [];
    public static function create(int $value): self
    {
        if (!isset(self::$cached[$value])) {
            self::$cached[$value] = new self($value);
        }
        return self::$cached[$value];
    }

    private function __construct(private readonly int $value)
    {
    }
    public function getValue(): int
    {
        return $this->value;
    }
    public function getDisplayValue(?string $locale = null): string
    {
        return (string)$this->value;
    }
}
