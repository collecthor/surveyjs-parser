<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\IntegerValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final readonly class IntegerValueOption implements IntegerValueOptionInterface
{
    use GetDisplayValue;

    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private int $rawValue,
        private array $displayValues
    ) {
    }

    public function getValue(): int
    {
        return $this->rawValue;
    }
}
