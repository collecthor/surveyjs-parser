<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

final class MissingValue implements SpecialValueInterface
{
    private static self|null $singleton = null;
    public static function reset(): void
    {
        self::$singleton = null;
    }

    private function __construct()
    {
    }

    public static function create(): self
    {
        if (!isset(self::$singleton)) {
            self::$singleton = new MissingValue();
        }
        return self::$singleton;
    }
    public function getType(): ValueType
    {
        return ValueType::Missing;
    }

    public function getValue(): null
    {
        return null;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return '';
    }
}
