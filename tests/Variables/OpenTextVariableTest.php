<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\DataInterfaces\InvalidValueInterface;
use Collecthor\DataInterfaces\MissingValueInterface;
use Collecthor\DataInterfaces\NumericValueInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Variables\NumericVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Values\InvalidValue
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\DataInterfaces\MissingValueInterface
 * @uses \Collecthor\SurveyjsParser\Values\MissingStringValue
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 */
class OpenTextVariableTest extends TestCase
{
    /**
     * @return iterable<list<mixed>>
     */
    public function recordProvider(): iterable
    {
        yield ["", MissingValueInterface::class, ['abc' => "15"]];
        yield ["15", StringValueInterface::class, ['path' => 15]];
        yield ["test", StringValueInterface::class, ['path' => "test"]];
    }

    /**
     * @dataProvider recordProvider
     * @param class-string $expectedClass
     * @param array<mixed> $sample
     */
    public function testGetValue(mixed $expected, string $expectedClass, array $sample): void
    {
        $variable = new OpenTextVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getValue(new ArrayRecord($sample, 1, new \DateTime(), new \DateTime()));

        self::assertInstanceOf($expectedClass, $value);
        self::assertSame($expected, $value->getRawValue());
    }

    public function testGetMeasure(): void
    {
        $variable = new OpenTextVariable('abc', [], ['path']);
        self::assertSame("nominal", $variable->getMeasure());
    }

    /**
     * @dataProvider recordProvider
     * @param class-string $expectedClass
     * @param array<mixed> $sample
     */
    public function testGetDisplayValue(string $expected, string $expectedClass, array $sample): void
    {
        $variable = new OpenTextVariable('abc', ['en' => 'English', 'nl' => 'Dutch'], ['path']);

        $value = $variable->getDisplayValue(new ArrayRecord($sample, 1, new \DateTime(), new \DateTime()));

        self::assertSame($expected, $value->getRawValue());
    }
}
