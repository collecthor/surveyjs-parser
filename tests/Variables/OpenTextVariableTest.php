<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(OpenTextVariable::class)]
final class OpenTextVariableTest extends VariableTestBase
{
    /**
     * @return iterable<array{0: null|string, 1: class-string, 2: array<string|int>}>
     */
    public static function recordProvider(): iterable
    {
        yield [null, SpecialValueInterface::class, ['abc' => "15"]];
        yield ["15", StringValueInterface::class, ['path' => 15]];
        yield ["test", StringValueInterface::class, ['path' => "test"]];
    }

    /**
     * @param class-string $expectedClass
     * @param array<string, string|int> $sample
     */
    #[DataProvider('recordProvider')]
    public function testGetValue(mixed $expected, string $expectedClass, array $sample): void
    {
        $variable = new OpenTextVariable('abc', ['path'], ['en' => 'English', 'nl' => 'Dutch']);

        $value = $variable->getValue(new ArrayDataRecord($sample));

        self::assertInstanceOf($expectedClass, $value);

        self::assertSame($expected, $value->getValue());
    }

    public function testGetMeasure(): void
    {
        $variable = new OpenTextVariable(name: 'abc', dataPath: ['path']);
        self::assertSame(Measure::Nominal, $variable->getMeasure());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new OpenTextVariable(
            'abc',
            dataPath: ['path'],
            titles: ['en' => 'English', 'nl' => 'Dutch'],
            rawConfiguration: $rawConfiguration
        );
    }

    protected function getVariableWithName(string $name): OpenTextVariable
    {
        return new OpenTextVariable($name, dataPath: ['path'], titles: ['en' => 'English', 'nl' => 'Dutch']);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new OpenTextVariable('abc', dataPath: ['path'], titles: $titles);
    }
}
