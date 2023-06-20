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
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\Values\ValueSet
 * @uses \Collecthor\SurveyjsParser\Values\NotNormalValue
 */
final class MultipleChoiceVariableTest extends TestCase
{
    public function testMeasureIsNominal(): void
    {
        $option = new IntegerValueOption(15, []);
        $subject = new MultipleChoiceVariable("test", [], [$option], ['path']);
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
        $subject = new MultipleChoiceVariable('test', [], $valueOptions, ['path']);

        $data = new ArrayDataRecord(['path' => ['test', 'bad data']]);

        $foundValue = $subject->getValue($data);

        self::assertSame(ValueType::Invalid, $foundValue->getType());
        self::assertSame($data->getDataValue(['path']), $foundValue->getRawValue());
    }

    public function testGetInvalidValueType(): void
    {
        $valueOptions = [
            new StringValueOption('test', ['en' => 'test', 'nl' => 'testnl']),
            new StringValueOption('test2', ['en' => 'test-2', 'nl' => 'testnl2']),
            new StringValueOption('test3', ['en' => 'test-3', 'nl' => 'testnl3']),
            new StringValueOption('test4', ['en' => 'test-4', 'nl' => 'testnl4']),
        ];
        $subject = new MultipleChoiceVariable('test', [], $valueOptions, ['path']);

        $data = new ArrayDataRecord(['path' => ['test', ['a' => 'b']]]);

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
        $subject = new MultipleChoiceVariable('test', [], $valueOptions, ['path']);

        $data = new ArrayRecord(['path' => ['test', 'test2']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertSame(ValueType::Normal, $foundValue->getType());
        self::assertInstanceOf(ValueSet::class, $foundValue);
        self::assertSame($valueOptions[0], $foundValue->getValue()[0]);
        self::assertSame($valueOptions[1], $foundValue->getValue()[1]);
    }
}
