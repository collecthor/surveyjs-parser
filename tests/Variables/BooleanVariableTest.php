<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Values\BooleanValueOption;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\BooleanVariable
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\Values\BooleanValueOption
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\Values\NotNormalValue
 */

final class BooleanVariableTest extends TestCase
{
    public function testMeasureIsNominal(): void
    {
        $subject = new BooleanVariable(
            "test",
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
            ['path']
        );

        self::assertSame(Measure::Nominal, $subject->getMeasure());
    }

    public function testInvalidValue(): void
    {
        $subject = new BooleanVariable(
            "test",
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
            ['path']
        );

        $record = new ArrayDataRecord(['path' => 'some string']);

        $value = $subject->getValue($record);

        self::assertSame(ValueType::Invalid, $value->getType());
    }

    public function testValidValue(): void
    {
        $subject = new BooleanVariable(
            "test",
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
            ['path']
        );

        $record = new ArrayRecord(['path' => true], 1, new \DateTime(), new \DateTime());

        $value = $subject->getValue($record);

        self::assertInstanceOf(BooleanValueOption::class, $value);
        self::assertTrue($value->getRawValue());
    }

    public function testNullValue(): void
    {
        $subject = new BooleanVariable(
            "test",
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
            ['path']
        );

        $record = new ArrayRecord(['path' => null], 1, new \DateTime(), new \DateTime());

        $value = $subject->getValue($record);

        self::assertSame(ValueType::Missing, $value->getType());
    }
}
