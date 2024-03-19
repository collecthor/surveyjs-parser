<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;

final readonly class InvalidValue extends StringValue implements SpecialValueInterface
{
    public function getType(): ValueType
    {
        return ValueType::Invalid;
    }
}
