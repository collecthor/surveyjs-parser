<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\MultipleChoiceValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipleChoiceVariable::class)]
final class MultipleChoiceVariableTest extends TestCase
{
    public function testMeasureIsNominal(): void
    {
        $option = new IntegerValueOption(15, []);
        $subject = new MultipleChoiceVariable("test", dataPath: ['path'], options: [$option]);
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
        $subject = new MultipleChoiceVariable('test', dataPath: ['path'], options: $valueOptions);
        $data = new ArrayDataRecord(['path' => ['test', 'bad data']]);

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(SpecialValueInterface::class, $foundValue);
        self::assertSame(ValueType::Invalid, $foundValue->getType());
        self::assertSame(StringValue::toString($data->getDataValue(['path'])), $foundValue->getValue());
    }

    public function testGetInvalidValueType(): void
    {
        $valueOptions = [
            new StringValueOption('test', ['en' => 'test', 'nl' => 'testnl']),
            new StringValueOption('test2', ['en' => 'test-2', 'nl' => 'testnl2']),
            new StringValueOption('test3', ['en' => 'test-3', 'nl' => 'testnl3']),
            new StringValueOption('test4', ['en' => 'test-4', 'nl' => 'testnl4']),
        ];
        $subject = new MultipleChoiceVariable('test', dataPath: ['path'], options: $valueOptions);

        $data = new ArrayDataRecord(['path' => ['test', ['a' => 'b']]]);

        $foundValue = $subject->getValue($data);
        self::assertInstanceOf(SpecialValueInterface::class, $foundValue);
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
        $subject = new MultipleChoiceVariable('test', dataPath: ['path'], options: $valueOptions);

        $data = new ArrayRecord(['path' => ['test', 'test2']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(MultipleChoiceValue::class, $foundValue);
        self::assertSame($valueOptions[0], $foundValue->getValue()[0]);
        self::assertSame($valueOptions[1], $foundValue->getValue()[1]);
    }
}
