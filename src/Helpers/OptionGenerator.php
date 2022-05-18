<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use UnitEnum;
use function iter\map;
use function iter\toArray;

class OptionGenerator
{
    /**
     * @return list<StringValueOption>
     */
    public static function generateOptionsFromUnitEnum(\UnitEnum $example): array
    {
        return toArray(map(fn (UnitEnum $case) => new StringValueOption($case->name, [ValueOptionInterface::DEFAULT_LOCALE => $case->name]), $example::cases()));
    }

    /**
     * @return list<StringValueOption>|list<IntegerValueOption>
     */
    public static function generateOptionsFromBackedEnum(\BackedEnum $example): array
    {
        $mapper = is_int($example->value)
            /** @phpstan-ignore-next-line  */
            ? fn (\BackedEnum $case) => new IntegerValueOption($case->value, [ValueOptionInterface::DEFAULT_LOCALE => $case->name])
            /** @phpstan-ignore-next-line  */
            : fn (\BackedEnum $case) => new StringValueOption($case->value, [ValueOptionInterface::DEFAULT_LOCALE => $case->name]);

        return toArray(map($mapper, $example::cases()));
    }
}
