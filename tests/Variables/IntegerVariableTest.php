<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Variables\IntegerVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IntegerVariable::class)]
class IntegerVariableTest extends VariableTestBase
{
    /**
     * @return iterable<array{0: int|null, 1: class-string, 2: array<string, float|string|int>}>
     */
    public static function recordProvider(): iterable
    {
        yield [null, SpecialValueInterface::class, ['abc' => "15"]];
        yield [15, IntegerValue::class, ['path' => 15]];
        yield [15, IntegerValue::class, ['path' => "15"]];
    }

    /**
     * @param class-string $expectedClass
     * @param array<string, mixed> $sample
     */
    #[DataProvider('recordProvider')]
    public function testGetValue(int|null $expected, string $expectedClass, array $sample): void
    {
        $variable = new IntegerVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getValue(new ArrayDataRecord($sample));

        self::assertInstanceOf($expectedClass, $value);
        self::assertSame($expected, $value->getValue());
    }

    public function testGetMeasure(): void
    {
        $variable = new IntegerVariable('abc', [], ['path']);
        self::assertSame(Measure::Ordinal, $variable->getMeasure());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new IntegerVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path'], rawConfiguration: $rawConfiguration);
    }


    protected function getVariableWithName(string $name, array $dataPath = ['path']): VariableInterface
    {
        return new IntegerVariable($name, ['en' => 'English', 'nl' => 'Dutch'], $dataPath);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new IntegerVariable('abc', $titles, ['path']);
    }
}
