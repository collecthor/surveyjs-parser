<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final class BooleanValueOption implements ValueOptionInterface
{
    use GetDisplayValue;
    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private readonly bool $rawValue,
        private readonly array $displayValues
    ) {
    }

    public function getRawValue(): bool
    {
        return $this->rawValue;
    }
}
