<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Tests\support\SimpleValueTest;
use Collecthor\SurveyjsParser\Values\MissingStringValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\MissingStringValue
 */
#[CoversClass(MissingStringValue::class)]
class MissingStringValueTest extends StringValueTest
{
    public function testSystemMissing(): void
    {
        $this->assertTrue((new MissingStringValue('', true))->isSystemMissing());
        $this->assertFalse((new MissingStringValue('', false))->isSystemMissing());
    }
}
