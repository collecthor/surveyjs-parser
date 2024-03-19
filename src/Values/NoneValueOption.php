<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\SpecialValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final class NoneValueOption implements SpecialValueOptionInterface
{
    use GetDisplayValue;

    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private readonly array $displayValues
    ) {
    }
    public function getType(): ValueType
    {
        return ValueType::None;
    }

    public function getValue(): string
    {
        return 'none';
    }
}
