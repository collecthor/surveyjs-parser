<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Helpers;

use Collecthor\DataInterfaces\Measure;
use Collecthor\SurveyjsParser\Helpers\OptionGenerator;
use Collecthor\SurveyjsParser\Tests\support\IntegerBackedEnumSample;
use Collecthor\SurveyjsParser\Tests\support\StringBackedEnumSample;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Helpers\OptionGenerator
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 */
class OptionGeneratorTest extends TestCase
{
    public function testGenerateFromUnitEnum(): void
    {
        $options = OptionGenerator::generateOptionsFromUnitEnum(Measure::Scale);

        self::assertCount(3, $options);
        self::assertSame(Measure::Nominal->name, $options[0]->getDisplayValue());
        self::assertSame(Measure::Nominal->name, $options[0]->getRawValue());

        self::assertSame(Measure::Ordinal->name, $options[1]->getDisplayValue());
        self::assertSame(Measure::Ordinal->name, $options[1]->getRawValue());

        self::assertSame(Measure::Scale->name, $options[2]->getDisplayValue());
        self::assertSame(Measure::Scale->name, $options[2]->getRawValue());
    }

    public function testGenerateFromStringBackedEnum(): void
    {
        $options = OptionGenerator::generateOptionsFromBackedEnum(StringBackedEnumSample::Case1);

        self::assertCount(2, $options);
        self::assertSame(StringBackedEnumSample::Case1->name, $options[0]->getDisplayValue());
        self::assertSame(StringBackedEnumSample::Case1->value, $options[0]->getRawValue());

        self::assertSame(StringBackedEnumSample::Case2->name, $options[1]->getDisplayValue());
        self::assertSame(StringBackedEnumSample::Case2->value, $options[1]->getRawValue());
    }

    public function testGenerateFromIntegerBackedEnum(): void
    {
        $options = OptionGenerator::generateOptionsFromBackedEnum(IntegerBackedEnumSample::Case1);

        self::assertCount(2, $options);
        self::assertSame(IntegerBackedEnumSample::Case1->name, $options[0]->getDisplayValue());
        self::assertSame(IntegerBackedEnumSample::Case1->value, $options[0]->getRawValue());

        self::assertSame(IntegerBackedEnumSample::Case2->name, $options[1]->getDisplayValue());
        self::assertSame(IntegerBackedEnumSample::Case2->value, $options[1]->getRawValue());
    }
}
