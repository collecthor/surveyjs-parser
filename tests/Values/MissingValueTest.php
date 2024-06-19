<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Values\MissingValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MissingValue::class)]
final class MissingValueTest extends TestCase
{
    public function testThatItIsASingleton(): void
    {
        MissingValue::reset();
        self::assertSame(MissingValue::create(), MissingValue::create());
    }

    public function testValueType(): void
    {
        self::assertSame(ValueType::Missing, MissingValue::create()->getType());
    }

    public function testDisplayValues(): void
    {
        self::assertSame('', MissingValue::create()->getDisplayValue());
    }
}
