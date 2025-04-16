<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Variables\FloatVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(FloatVariable::class)]
final class FloatVariableTest extends VariableTestBase
{
    /**
     * @return iterable<array{0: string, 1: class-string, 2: array<string, float|string|int>, 3: int|float|string|null}>
     */
    public static function recordProvider(): iterable
    {
        yield [(string)PHP_INT_MIN, SpecialValueInterface::class, ['abc' => "15"], null];
        yield ["15", IntegerValue::class, ['path' => 15], 15];
        yield ["15.40", FloatValue::class, ['path' => 15.4], 15.4];
        yield ["15", SpecialValueInterface::class, ['path' => "15"], "15"];
    }

    /**
     * @param class-string $expectedClass
     * @param array<string, mixed> $sample
     */
    #[DataProvider('recordProvider')]
    public function testGetValue(string $expected, string $expectedClass, array $sample, mixed $expectedRaw): void
    {
        $variable = new FloatVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getValue(new ArrayDataRecord($sample));

        self::assertInstanceOf($expectedClass, $value);
        self::assertSame($expectedRaw, $value->getValue());
    }

    public function testGetMeasure(): void
    {
        $variable = new FloatVariable('abc', [], ['path']);
        self::assertSame(Measure::Ordinal, $variable->getMeasure());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new FloatVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path'], $rawConfiguration);
    }


    protected function getVariableWithName(string $name, array $dataPath = ['path']): VariableInterface
    {
        return new FloatVariable($name, ['en' => 'English', 'nl' => 'Dutch'], $dataPath);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new FloatVariable('abc', $titles, ['path']);
    }
}
