<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Tests\support\SimpleValueTests;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\IntegerValue
 */
#[CoversClass(IntegerValue::class)]
class IntegerValueTest extends TestCase
{
    use SimpleValueTests;


    /**
     * @return iterable<list<int>>
     */
    public static function getValidSamples(): iterable
    {
        yield [30];
    }

    /**
     * @return iterable<mixed>
     */
    public static function getInvalidSamples(): iterable
    {
        yield [30.14];
    }
}
