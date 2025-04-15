<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use BackedEnum;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use UnitEnum;

class OptionGenerator
{
    /**
     * @return list<StringValueOption>
     */
    public static function generateOptionsFromUnitEnum(UnitEnum $example): array
    {
        $mapper = static fn (UnitEnum $case): StringValueOption => new StringValueOption($case->name, [ValueOptionInterface::DEFAULT_LOCALE => $case->name]);
        return array_map($mapper, $example::cases());
    }

    /**
     * @return list<StringValueOption>|list<IntegerValueOption>
     */
    public static function generateOptionsFromBackedEnum(BackedEnum $example): array
    {
        $result = [];
        foreach ($example::cases() as $case) {
            $result[] = is_int($case->value)
                ? new IntegerValueOption($case->value, [ValueOptionInterface::DEFAULT_LOCALE => $case->name])
                : new StringValueOption($case->value, [ValueOptionInterface::DEFAULT_LOCALE => $case->name]);
        }

        return $result;
    }
}
