<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Tests\support\SimpleValueTests;
use Collecthor\SurveyjsParser\Values\StringValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\StringValue
 */
#[CoversClass(StringValue::class)]
class StringValueTest extends TestCase
{
    use SimpleValueTests;

    /**
     * @return iterable<list<string>>
     */
    public static function getValidSamples(): iterable
    {
        yield ["test"];
        yield ["test with spaces"];
    }

    /**
     * @return iterable<list<string|int|float|list<string>>>
     */
    public static function getInvalidSamples(): iterable
    {
        yield [13];
        yield [1545.4];
        yield [['array value']];
    }
}
