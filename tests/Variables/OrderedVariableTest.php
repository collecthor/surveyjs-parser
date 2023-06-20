<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Values\ValueSet;
use Collecthor\SurveyjsParser\Variables\OrderedVariable;
use PHPUnit\Framework\TestCase;
use function PHPStan\dumpType;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\OrderedVariable
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\Values\ValueSet
 * @uses \Collecthor\SurveyjsParser\Values\NotNormalValue
 */
final class OrderedVariableTest extends TestCase
{
    public function testMeasureIsNominal(): void
    {
        $option = new IntegerValueOption(15, []);
        $subject = new OrderedVariable("test", [], [$option], ['path']);
        self::assertSame(Measure::Nominal, $subject->getMeasure());
    }

    public function testGetInvalidValues(): void
    {
        $valueOptions = [
            new StringValueOption('test', ['en' => 'test', 'nl' => 'testnl']),
            new StringValueOption('test2', ['en' => 'test-2', 'nl' => 'testnl2']),
            new StringValueOption('test3', ['en' => 'test-3', 'nl' => 'testnl3']),
            new StringValueOption('test4', ['en' => 'test-4', 'nl' => 'testnl4']),
        ];
        $subject = new OrderedVariable('test', [], $valueOptions, ['path']);

        $data = new ArrayRecord(['path' => ['test', 'bad data']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertSame(ValueType::Invalid, $foundValue->getType());
    }

    public function testGetValidValues(): void
    {
        $valueOptions = [
            new StringValueOption('test', ['en' => 'test', 'nl' => 'testnl']),
            new StringValueOption('test2', ['en' => 'test-2', 'nl' => 'testnl2']),
            new StringValueOption('test3', ['en' => 'test-3', 'nl' => 'testnl3']),
            new StringValueOption('test4', ['en' => 'test-4', 'nl' => 'testnl4']),
        ];
        $subject = new OrderedVariable('test', [], $valueOptions, ['path']);

        $data = new ArrayRecord(['path' => ['test', 'test2']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(ValueSet::class, $foundValue);

        self::assertSame($valueOptions[0], $foundValue->getValue()[0]);
        self::assertSame($valueOptions[1], $foundValue->getValue()[1]);
    }

    public function testGetCorrectOrdering(): void
    {
        $valueOptions = [
            new StringValueOption('test', ['en' => 'test', 'nl' => 'testnl']),
            new StringValueOption('test2', ['en' => 'test-2', 'nl' => 'testnl2']),
            new StringValueOption('test3', ['en' => 'test-3', 'nl' => 'testnl3']),
            new StringValueOption('test4', ['en' => 'test-4', 'nl' => 'testnl4']),
        ];
        $subject = new OrderedVariable('test', [], $valueOptions, ['path']);

        $data = new ArrayRecord(['path' => ['test', 'test2', 'test3', 'test4']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(ValueSet::class, $foundValue);

        /** @var StringValueOption[] $values */
        $values = $foundValue->getValue();

        self::assertSame($values[0]->getRawValue(), 'test');
        self::assertSame($values[1]->getRawValue(), 'test2');
        self::assertSame($values[2]->getRawValue(), 'test3');
        self::assertSame($values[3]->getRawValue(), 'test4');

        $data = new ArrayDataRecord(['path' => ['test3', 'test4', 'test', 'test2']]);

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(ValueSet::class, $foundValue);

        /** @var StringValueOption[] $values */
        $values = $foundValue->getValue();

        self::assertSame($values[0]->getRawValue(), 'test3');
        self::assertSame($values[1]->getRawValue(), 'test4');
        self::assertSame($values[2]->getRawValue(), 'test');
        self::assertSame($values[3]->getRawValue(), 'test2');
    }
}
