<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Values\ValueSet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\ValueSet
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 */

final class ValueSetTest extends TestCase
{
    public function testCreateValueSet(): void
    {
        $values = [
           new StringValueOption('test', ['nl' => 'test']),
           new StringValueOption('test2', ['nl' => 'test2']),
        ];
        $set = new ValueSet(...$values);

        self::assertCount(count($values), $set->getValue());
        self::assertSame($values, $set->getValue());
    }
}
