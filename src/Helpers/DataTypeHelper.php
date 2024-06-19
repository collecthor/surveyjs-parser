<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\Interfaces\BaseValueInterface;
use Collecthor\SurveyjsParser\Interfaces\BooleanVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\MultipleChoiceVariableInterface;
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

    /**
     * @phpstan-assert-if-true MultipleChoiceVariableInterface $variable
     */
    public static function isMultipleChoice(VariableInterface $variable): bool
    {
        return $variable instanceof MultipleChoiceVariableInterface;
    }

    /**
     * @phpstan-assert-if-true ClosedVariableInterface $variable
     */
    public static function isClosed(VariableInterface $variable): bool
    {
        return $variable instanceof ClosedVariableInterface;
    }
}
