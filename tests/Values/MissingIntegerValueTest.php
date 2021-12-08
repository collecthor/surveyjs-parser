<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Tests\support\SimpleValueTest;
use Collecthor\SurveyjsParser\Values\MissingIntegerValue;
use Collecthor\SurveyjsParser\Values\MissingStringValue;
use PHPUnit\Framework\TestCase;

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
