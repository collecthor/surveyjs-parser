<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArrayDataRecord::class)]
class ArrayDataRecordTest extends TestCase
{
    /**
     * @return iterable<array{0:array<string, mixed>, 1:non-empty-list<string>, 2:mixed}>
     */
    public static function dataProvider(): iterable
    {
        yield [['a' => 'b'], ['a'], 'b'];
        yield [['a' => 'b'], ['c'], null];
        yield [['a' => ['b' => 'c']], ['a', 'b'], 'c'];
        yield [['a' => ['b' => ['c' => ['d' => ['e' => ['f' => ['g' => 15]]]]]]], ['a', 'b', 'c', 'd', 'e', 'f', 'g'], 15];
    }

    /**
     * @param array<string, mixed> $exampleData
     * @param non-empty-list<string> $path
     *
     */
    #[DataProvider('dataProvider')]
    public function testGetDataValue(array $exampleData, array $path, mixed $value): void
    {
        $subject = new ArrayDataRecord($exampleData);
        self::assertSame($value, $subject->getDataValue($path));
    }

    /**
     * @param array<string, mixed> $exampleData
     */
    #[DataProvider('dataProvider')]
    public function testAllData(array $exampleData): void
    {
        $subject = new ArrayDataRecord($exampleData);
        self::assertSame($exampleData, $subject->allData());
    }
}
