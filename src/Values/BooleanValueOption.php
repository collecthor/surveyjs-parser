<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\BooleanValueInterface;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final class BooleanValueOption implements BooleanValueInterface
{
    use GetDisplayValue;
    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private readonly bool|string $rawValue,
        private readonly bool $value,
        private readonly array $displayValues = []
    ) {
    }

    public function getRawValue(): bool|string
    {
        return $this->rawValue;
    }


    public function getValue(): bool
    {
        return $this->value;
    }
}
