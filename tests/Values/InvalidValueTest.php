<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Tests\support\CoversClass;
use Collecthor\SurveyjsParser\Values\InvalidValue;
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
        self::assertStringContainsString('abc', $value->getRawValue());
        self::assertStringContainsString('def', $value->getRawValue());
        self::assertStringContainsString('ghi', $value->getRawValue());
        self::assertStringContainsString('jkl', $value->getRawValue());
        self::assertStringContainsString('mno', $value->getRawValue());

        /** @phpstan-ignore-next-line  */
        self::assertTrue($value->isSystemMissing());
    }

    public function testFloat(): void
    {
        $value = new InvalidValue(15.4);
        self::assertSame("15.4", $value->getRawValue());
        /** @phpstan-ignore-next-line  */
        self::assertTrue($value->isSystemMissing());
    }

    public function testInteger(): void
    {
        $value = new InvalidValue(14);
        self::assertSame("14", $value->getRawValue());
        /** @phpstan-ignore-next-line  */
        self::assertTrue($value->isSystemMissing());
    }

    public function testString(): void
    {
        $randomString = random_bytes(15);
        $value = new InvalidValue($randomString);
        self::assertSame($randomString, $value->getRawValue());
        /** @phpstan-ignore-next-line  */
        self::assertTrue($value->isSystemMissing());
    }
}
