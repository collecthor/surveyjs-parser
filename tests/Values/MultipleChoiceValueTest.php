<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Values\MultipleChoiceValue;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipleChoiceValue::class)]
class MultipleChoiceValueTest extends TestCase
{
    public function testGetValue(): void
    {
        $o = new StringValueOption('test', []);
        $v = new MultipleChoiceValue($o);

        self::assertSame([$o], $v->getValue());
    }

    public function testGetIndex(): void
    {
        $o1 = new StringValueOption('test1', []);
        $o2 = new StringValueOption('test2', []);
        $v = new MultipleChoiceValue($o1, $o2);

        self::assertSame($o1, $v->getIndex(0));
        self::assertSame($o2, $v->getIndex(1));
        self::assertNull($v->getIndex(3));
    }
}
