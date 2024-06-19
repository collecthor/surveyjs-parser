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
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipleChoiceVariable::class)]
final class OrderedVariableTest extends TestCase
{
    public function testMeasureIsNominal(): void
    {
        $option = new IntegerValueOption(15, []);
        $subject = new MultipleChoiceVariable("test", options: [$option], dataPath: ['path'], ordered: true);
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
        $subject = new MultipleChoiceVariable('test', options: $valueOptions, dataPath: ['path'], ordered: true);

        $data = new ArrayRecord(['path' => ['test', 'bad data']], 1, new \DateTime(), new \DateTime());

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
        $subject = new MultipleChoiceVariable('test', dataPath: ['path'], options: $valueOptions, ordered: true);

        $data = new ArrayRecord(['path' => ['test', 'test2']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(MultipleChoiceValue::class, $foundValue);

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
        $subject = new MultipleChoiceVariable('test', dataPath: ['path'], options: $valueOptions, ordered: true);

        $data = new ArrayRecord(['path' => ['test', 'test2', 'test3', 'test4']], 1, new \DateTime(), new \DateTime());

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(MultipleChoiceValue::class, $foundValue);

        /** @var StringValueOption[] $values */
        $values = $foundValue->getValue();

        self::assertSame($values[0]->getValue(), 'test');
        self::assertSame($values[1]->getValue(), 'test2');
        self::assertSame($values[2]->getValue(), 'test3');
        self::assertSame($values[3]->getValue(), 'test4');

        $data = new ArrayDataRecord(['path' => ['test3', 'test4', 'test', 'test2']]);

        $foundValue = $subject->getValue($data);

        self::assertInstanceOf(MultipleChoiceValue::class, $foundValue);

        /** @var StringValueOption[] $values */
        $values = $foundValue->getValue();

        self::assertSame($values[0]->getValue(), 'test3');
        self::assertSame($values[1]->getValue(), 'test4');
        self::assertSame($values[2]->getValue(), 'test');
        self::assertSame($values[3]->getValue(), 'test2');
    }
}
