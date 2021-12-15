<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Values\MissingIntegerValue;

/**
 * @covers \Collecthor\SurveyjsParser\Values\MissingIntegerValue
 */
#[CoversClass(MissingIntegerValue::class)]
class MissingIntegerValueTest extends IntegerValueTest
{
    public function testSystemMissing(): void
    {
        self::assertTrue((new MissingIntegerValue(14, true))->isSystemMissing());
        self::assertFalse((new MissingIntegerValue(13, false))->isSystemMissing());
    }
}
