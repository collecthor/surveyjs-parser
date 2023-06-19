<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

/**
 * @implements ValueOptionInterface<integer>
 */
final class IntegerValueOption implements ValueOptionInterface
{
    use GetDisplayValue;
    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private readonly int $rawValue,
        private readonly array $displayValues
    ) {
    }

    public function getRawValue(): int
    {
        return $this->rawValue;
    }

    public function isOther(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function getType(): ValueType
    {
        return ValueType::Normal;
    }

    public function getValue(): int
    {
        return $this->rawValue;
    }
}
