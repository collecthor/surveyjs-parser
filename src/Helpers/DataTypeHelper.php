<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\Interfaces\BaseValueInterface;
use Collecthor\SurveyjsParser\Interfaces\BooleanVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;

final class DataTypeHelper
{
    /**
     * @phpstan-assert-if-true BooleanVariableInterface $variable
     */
    public static function isBool(VariableInterface $variable): bool
    {
        return $variable instanceof BooleanVariableInterface;
    }


    /**
     * @phpstan-assert-if-false SpecialValueInterface $value
     */
    public static function valueIsNormal(BaseValueInterface $value): bool
    {
        return !$value instanceof SpecialValueInterface;
    }
}
