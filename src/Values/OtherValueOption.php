<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Traits\GetDisplayValue;

final readonly class OtherValueOption implements ValueOptionInterface, SpecialValueInterface
{
    use GetDisplayValue;

    private const RAW_VALUE = 'other';

    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private array $displayValues
    ) {
    }

    public function getType(): ValueType
    {
        return ValueType::Other;
    }

    public function getValue(): string
    {
        return self::RAW_VALUE;
    }
}
