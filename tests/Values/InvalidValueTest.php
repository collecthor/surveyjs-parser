<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Tests\support\SimpleValueTest;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\InvalidValue
 *
 * Below is for PHPUnit 10, and we use it for automated test generation in the trait.
 */
#[CoversClass(InvalidValue::class)]
class InvalidValueTest extends TestCase
{
    public function testArray(): void
    {
        $value = new InvalidValue(['abc', 'def' => 'ghi', 'jkl' => ['mno']]);
        $this->assertStringContainsString('abc', $value->getRawValue());
        $this->assertStringContainsString('def', $value->getRawValue());
        $this->assertStringContainsString('ghi', $value->getRawValue());
        $this->assertStringContainsString('jkl', $value->getRawValue());
        $this->assertStringContainsString('mno', $value->getRawValue());

        $this->assertTrue($value->isSystemMissing());
    }

    public function testFloat(): void
    {
        $value = new InvalidValue(15.4);
        $this->assertSame("15.4", $value->getRawValue());

        $this->assertTrue($value->isSystemMissing());
    }

    public function testInteger(): void
    {
        $value = new InvalidValue(14);
        $this->assertSame("14", $value->getRawValue());

        $this->assertTrue($value->isSystemMissing());
    }

    public function testString(): void
    {
        $randomString = random_bytes(15);
        $value = new InvalidValue($randomString);
        $this->assertSame($randomString, $value->getRawValue());

        $this->assertTrue($value->isSystemMissing());
    }
}
