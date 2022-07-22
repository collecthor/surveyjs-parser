<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

class StringValueOption implements ValueOptionInterface
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
}
