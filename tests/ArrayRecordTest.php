<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\ArrayRecord;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\ArrayRecord
 */
class ArrayRecordTest extends TestCase
{
    public function testGetRecordId(): void
    {
        $id = mt_rand();
        $subject = new ArrayRecord([], $id, new \DateTime(), new \DateTime());

        self::assertSame($id, $subject->getRecordId());
    }

    public function testGetStarted(): void
    {
        $dateTime = new \DateTime('@' . mt_rand(0, 2 ^ 32 - 1));
        $subject = new ArrayRecord([], 0, $dateTime, new \DateTime());

        self::assertEquals($dateTime, $subject->getStarted());
    }

    public function testGetLastUpdate(): void
    {
        $dateTime = new \DateTime('@' . mt_rand(0, 2 ^ 32 - 1));
        $subject = new ArrayRecord([], 0, new \DateTime(), $dateTime);

        self::assertEquals($dateTime, $subject->getLastUpdate());
    }

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
        $subject = new ArrayRecord($exampleData, 0, new \DateTime(), new \DateTime());
        self::assertSame($value, $subject->getDataValue($path));
    }
}
