<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\DataInterfaces\InvalidValueInterface;
use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\MissingValueInterface;
use Collecthor\DataInterfaces\NumericValueInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Variables\NumericVariable;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\NumericVariable
 * @uses \Collecthor\SurveyjsParser\Values\InvalidValue
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValue
 * @uses \Collecthor\SurveyjsParser\Values\FloatValue
 * @uses \Collecthor\DataInterfaces\MissingValueInterface
 * @uses \Collecthor\SurveyjsParser\Values\MissingIntegerValue
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 */
class NumericVariableTest extends VariableTest
{
    /**
     * @return iterable<list<mixed>>
     */
    public function recordProvider(): iterable
    {
        yield [PHP_INT_MIN, MissingValueInterface::class, ['abc' => "15"]];
        yield [15, NumericValueInterface::class, ['path' => 15]];
        yield [15.4, NumericValueInterface::class, ['path' => 15.4]];
        yield ["15", InvalidValueInterface::class, ['path' => "15"]];
        yield [PHP_INT_MIN, MissingValueInterface::class, ['abc' => "15"]];
    }

    /**
     * @dataProvider recordProvider
     * @param class-string $expectedClass
     * @param array<mixed> $sample
     */
    public function testGetValue(mixed $expected, string $expectedClass, array $sample): void
    {
        $variable = new NumericVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getValue(new ArrayRecord($sample, 1, new \DateTime(), new \DateTime()));

        self::assertInstanceOf($expectedClass, $value);
        self::assertSame($expected, $value->getRawValue());
    }

    public function testGetMeasure(): void
    {
        $variable = new NumericVariable('abc', [], ['path']);
        self::assertSame(Measure::Ordinal, $variable->getMeasure());
    }

    /**
     * @dataProvider recordProvider
     * @param class-string $expectedClass
     * @param array<mixed> $sample
     */
    public function testGetDisplayValue(string|int|float $expected, string $expectedClass, array $sample): void
    {
        $variable = new NumericVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getDisplayValue(new ArrayRecord($sample, 1, new \DateTime(), new \DateTime()));

        self::assertSame((string) $expected, $value->getRawValue());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new NumericVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path'], $rawConfiguration);
    }


    protected function getVariableWithName(string $name): JavascriptVariableInterface
    {
        return new NumericVariable($name, ['en' => 'English', 'nl' => 'Dutch'], ['path']);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new NumericVariable('abc', $titles, ['path']);
    }
}
