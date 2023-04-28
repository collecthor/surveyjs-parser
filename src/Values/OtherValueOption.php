<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final class OtherValueOption implements \Collecthor\DataInterfaces\ValueOptionInterface
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
}
