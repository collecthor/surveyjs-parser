<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final class StringValueOption implements ValueOptionInterface
{
    use GetDisplayValue;
    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private readonly string $rawValue,
        private readonly array $displayValues
    ) {
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getValue(): string
    {
        return $this->rawValue;
    }
    public function getSerialized(): string
    {
        return $this->rawValue;
    }
}
