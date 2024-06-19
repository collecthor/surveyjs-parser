<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Helpers;

use Collecthor\SurveyjsParser\Helpers\OptionGenerator;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Tests\support\IntegerBackedEnumSample;
use Collecthor\SurveyjsParser\Tests\support\StringBackedEnumSample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OptionGenerator::class)]
class OptionGeneratorTest extends TestCase
{
    public function testGenerateFromUnitEnum(): void
    {
        $options = OptionGenerator::generateOptionsFromUnitEnum(Measure::Scale);

        self::assertCount(3, $options);
        self::assertSame(Measure::Nominal->name, $options[0]->getDisplayValue());
        self::assertSame(Measure::Nominal->name, $options[0]->getValue());

        self::assertSame(Measure::Ordinal->name, $options[1]->getDisplayValue());
        self::assertSame(Measure::Ordinal->name, $options[1]->getValue());

        self::assertSame(Measure::Scale->name, $options[2]->getDisplayValue());
        self::assertSame(Measure::Scale->name, $options[2]->getValue());
    }

    public function testGenerateFromStringBackedEnum(): void
    {
        $options = OptionGenerator::generateOptionsFromBackedEnum(StringBackedEnumSample::Case1);

        self::assertCount(2, $options);
        self::assertSame(StringBackedEnumSample::Case1->name, $options[0]->getDisplayValue());
        self::assertSame(StringBackedEnumSample::Case1->value, $options[0]->getValue());

        self::assertSame(StringBackedEnumSample::Case2->name, $options[1]->getDisplayValue());
        self::assertSame(StringBackedEnumSample::Case2->value, $options[1]->getValue());
    }

    public function testGenerateFromIntegerBackedEnum(): void
    {
        $options = OptionGenerator::generateOptionsFromBackedEnum(IntegerBackedEnumSample::Case1);

        self::assertCount(2, $options);
        self::assertSame(IntegerBackedEnumSample::Case1->name, $options[0]->getDisplayValue());
        self::assertSame(IntegerBackedEnumSample::Case1->value, $options[0]->getValue());

        self::assertSame(IntegerBackedEnumSample::Case2->name, $options[1]->getDisplayValue());
        self::assertSame(IntegerBackedEnumSample::Case2->value, $options[1]->getValue());
    }
}
