<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Values\BooleanValueOption;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[UsesClass(InvalidValue::class)]
#[UsesClass(MissingValue::class)]
#[UsesClass(StringValue::class)]
#[UsesClass(ArrayRecord::class)]
#[UsesClass(ArrayDataRecord::class)]
#[UsesClass(BooleanValueOption::class)]
#[CoversClass(BooleanVariable::class)]
final class BooleanVariableTest extends VariableTestBase
{
    public function testInvalidValue(): void
    {
        $subject = new BooleanVariable(
            "test",
            ['path'],
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
        );

        $record = new ArrayDataRecord(['path' => 'some string']);

        $value = $subject->getValue($record);

        self::assertInstanceOf(SpecialValueInterface::class, $value);
        self::assertSame(ValueType::Invalid, $value->getType());
    }

    public function testValidValue(): void
    {
        $subject = new BooleanVariable(
            "test",
            ['path'],
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
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
            ['path'],
            [],
            [
                "default" => "true",
                "nl" => "waar",
            ],
            [
                "default" => "false",
                "nl" => "onwaar",
            ],
        );

        $record = new ArrayRecord(['path' => null], 1, new \DateTime(), new \DateTime());

        $value = $subject->getValue($record);
        self::assertInstanceOf(SpecialValueInterface::class, $value);
        self::assertSame(ValueType::Missing, $value->getType());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new BooleanVariable(name: 'test', dataPath: ['path'], rawConfiguration: $rawConfiguration);
    }

    protected function getVariableWithName(string $name, array $dataPath = ['path']): VariableInterface
    {
        return new BooleanVariable(name: $name, dataPath: $dataPath);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new BooleanVariable(name: 'test', dataPath: ['path'], titles: $titles);
    }
}
