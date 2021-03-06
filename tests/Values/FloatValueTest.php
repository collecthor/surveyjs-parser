<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Tests\support\SimpleValueTest;
use Collecthor\SurveyjsParser\Values\FloatValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\FloatValue
 *
 * Below is for PHPUnit 10
 */
#[CoversClass(FloatValue::class)]
class FloatValueTest extends TestCase
{
    use SimpleValueTest;

    /**
     * @return iterable<mixed>
     */
    protected function getValidSamples(): iterable
    {
        yield [15.4];
    }

    /**
     * @return iterable<mixed>
     */
    public function getInvalidSamples(): iterable
    {
        yield ["string"];
        yield [[13]];
    }
}
