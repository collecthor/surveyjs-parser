<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

/**
 * @implements ValueOptionInterface<string>
 */
final class OtherValueOption implements ValueOptionInterface
{
    use GetDisplayValue;

    /**
     * @param string $rawValue
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private readonly string $rawValue,
        private readonly array $displayValues
    ) {
    }
    public function getRawValue(): string|int|float|bool
    {
        return $this->rawValue;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function isOther(): bool
    {
        return true;
    }

    public function getType(): ValueType
    {
        return ValueType::Normal;
    }

    public function getValue(): string
    {
        return $this->rawValue;
    }
}
