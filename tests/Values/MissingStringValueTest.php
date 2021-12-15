<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Values\MissingStringValue;

/**
 * @covers \Collecthor\SurveyjsParser\Values\MissingStringValue
 */
#[CoversClass(MissingStringValue::class)]
class MissingStringValueTest extends StringValueTest
{
    public function testSystemMissing(): void
    {
        self::assertTrue((new MissingStringValue('', true))->isSystemMissing());
        self::assertFalse((new MissingStringValue('', false))->isSystemMissing());
    }
}
