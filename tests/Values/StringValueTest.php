<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Values\StringValue;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(StringValue::class)]
class StringValueTest extends TestCase
{
    /**
     * @return iterable<list<array<mixed>|float|bool|int|string|null|\DateTimeInterface>>
     */
    public static function getValidSamples(): iterable
    {
        yield ["test"];
        yield ["test with spaces"];
        yield [13];
        yield [1545.4];
        yield [['array value']];
        yield [[new \DateTime('now')]];
        ;
        yield [[null]];
        yield [[true]];
        yield [[false]];
    }

    /**
     * @param array<mixed>|float|bool|int|string|\DateTimeInterface|null $param
     */
    #[DataProvider('getValidSamples')]
    public function testValidSamples(array|float|bool|int|string|null|\DateTimeInterface $param): void
    {
        $subject = new StringValue($param);
        Assert::assertSame(StringValue::toString($param), $subject->getValue());
        Assert::assertSame($param, $subject->getRawValue());
    }
}
