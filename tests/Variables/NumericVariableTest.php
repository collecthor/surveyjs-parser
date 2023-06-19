<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Variables\NumericVariable;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\NumericVariable
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValue
 * @uses \Collecthor\SurveyjsParser\Values\FloatValue
 * @uses \Collecthor\SurveyjsParser\Values\NotNormalValue
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 */
class NumericVariableTest extends VariableTestBase
{
    /**
     * @return iterable<array{0: string, 1: class-string, 2: array<string, float|string|int>, 3: int|float|string|null}>
     */
    public static function recordProvider(): iterable
    {
        yield [(string)PHP_INT_MIN, NotNormalValueInterface::class, ['abc' => "15"], null];
        yield ["15", IntegerValue::class, ['path' => 15], 15];
        yield ["15.40", FloatValue::class, ['path' => 15.4], 15.4];
        yield ["15", NotNormalValueInterface::class, ['path' => "15"], "15"];
    }

    /**
     * @dataProvider recordProvider
     * @param class-string $expectedClass
     * @param array<mixed> $sample
     */
    public function testGetValue(string $expected, string $expectedClass, array $sample, mixed $expectedRaw): void
    {
        $variable = new NumericVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getValue(new ArrayDataRecord($sample));

        self::assertInstanceOf($expectedClass, $value);
        self::assertSame($expectedRaw, $value->getRawValue());
    }

    public function testGetMeasure(): void
    {
        $variable = new NumericVariable('abc', [], ['path']);
        self::assertSame(Measure::Ordinal, $variable->getMeasure());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new NumericVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path'], $rawConfiguration);
    }


    protected function getVariableWithName(string $name): VariableInterface
    {
        return new NumericVariable($name, ['en' => 'English', 'nl' => 'Dutch'], ['path']);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new NumericVariable('abc', $titles, ['path']);
    }
}
