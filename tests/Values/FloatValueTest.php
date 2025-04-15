<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\SimpleValueTests;
use Collecthor\SurveyjsParser\Values\FloatValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FloatValue::class)]
final class FloatValueTest extends TestCase
{
    use SimpleValueTests;

    /**
     * @return iterable<non-empty-list<float>>
     */
    public static function getValidSamples(): iterable
    {
        yield [15.4];
    }

    /**
     * @return iterable<non-empty-list<mixed>>
     */
    public static function getInvalidSamples(): iterable
    {
        yield ["string"];
        yield [[13]];
    }
}
