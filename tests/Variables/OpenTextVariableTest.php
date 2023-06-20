<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\Values\NotNormalValue
 */
final class OpenTextVariableTest extends VariableTestBase
{
    /**
     * @return iterable<array{0: null|string, 1: class-string, 2: array<string|int>}>
     */
    public static function recordProvider(): iterable
    {
        yield [null, NotNormalValueInterface::class, ['abc' => "15"]];
        yield ["15", StringValueInterface::class, ['path' => 15]];
        yield ["test", StringValueInterface::class, ['path' => "test"]];
    }

    /**
     * @dataProvider recordProvider
     * @param class-string $expectedClass
     * @param array<string, string|int> $sample
     */
    public function testGetValue(mixed $expected, string $expectedClass, array $sample): void
    {
        $variable = new OpenTextVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getValue(new ArrayDataRecord($sample));

        self::assertInstanceOf($expectedClass, $value);

        self::assertSame($expected, $value->getRawValue());
        if ($value->getType() === ValueType::Normal) {
            self::assertSame($expected, $value->getValue());
        }
    }

    public function testGetMeasure(): void
    {
        $variable = new OpenTextVariable('abc', [], ['path']);
        self::assertSame(Measure::Nominal, $variable->getMeasure());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new OpenTextVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path'], $rawConfiguration);
    }

    protected function getVariableWithName(string $name): OpenTextVariable
    {
        return new OpenTextVariable($name, ['en' => 'English', 'nl' => 'Dutch'], ['path']);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new OpenTextVariable('abc', $titles, ['path']);
    }
}
