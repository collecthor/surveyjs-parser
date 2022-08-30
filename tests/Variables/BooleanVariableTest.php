<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\DataInterfaces\Measure;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Values\BooleanValue;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingBooleanValue;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\BooleanVariable
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\Values\InvalidValue
 * @uses \Collecthor\SurveyjsParser\Values\BooleanValue
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\Values\MissingBooleanValue
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

        $record = new ArrayRecord(['path' => 'some string'], 1, new \DateTime(), new \DateTime());

        $value = $subject->getValue($record);

        self::assertInstanceOf(InvalidValue::class, $value);
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

        self::assertInstanceOf(BooleanValue::class, $value);
        self::assertTrue($value->getRawValue());
    }

    public function testDisplayValue(): void
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

        self::assertInstanceOf(BooleanValue::class, $value);
        self::assertTrue($value->getRawValue());
        $displayValue = $subject->getDisplayValue($record)->getRawValue();
        self::assertEquals('true', $displayValue);

        $displayValue = $subject->getDisplayValue($record, 'nl')->getRawValue();
        self::assertEquals('waar', $displayValue);
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

        self::assertInstanceOf(MissingBooleanValue::class, $value);
    }
}
