<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\ArrayDataRecord
 */
class ArrayDataRecordTest extends TestCase
{
    /**
     * @phpstan-return iterable<array{0:array<string, mixed>, 1:non-empty-list<string>, 2:mixed}>
     */
    public function dataProvider(): iterable
    {
        yield [['a' => 'b'], ['a'], 'b'];
        yield [['a' => ['b' => 'c']], ['a', 'b'], 'c'];
    }

    /**
     * @dataProvider dataProvider
     * @param array<string, mixed> $exampleData
     * @param non-empty-list<string> $path
     * @param mixed $value
     * @return void
     */
    public function testGetDataValue(array $exampleData, array $path, mixed $value): void
    {
        $subject = new ArrayDataRecord($exampleData);
        self::assertSame($value, $subject->getDataValue($path));
    }

    /**
     * @dataProvider dataProvider
     * @param array<string, mixed> $exampleData
     */
    public function testAllData(array $exampleData): void
    {
        $subject = new ArrayDataRecord($exampleData);
        self::assertSame($exampleData, $subject->allData());
    }
}
