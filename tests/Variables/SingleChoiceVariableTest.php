<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(SingleChoiceVariable::class)]
class SingleChoiceVariableTest extends VariableTestBase
{
    public function testMeasureIsNominal(): void
    {
        $option = new IntegerValueOption(15, []);
        $subject = new SingleChoiceVariable("test", options: [$option], dataPath: ['path']);
        self::assertSame(Measure::Nominal, $subject->getMeasure());
    }

    public function testGetValueOptions(): void
    {
        /**
         * @var list<ValueOptionInterface> $options
         */
        $options = [];
        for ($i = 0; $i < 10; $i++) {
            if (mt_rand(0, 9) < 5) {
                $value = mt_rand();
                $option = new IntegerValueOption($value, ["en" => "Value: {$value}"]);
            } else {
                $value = bin2hex(random_bytes(16));
                $option = new StringValueOption($value, ["en" => "Value: {$value}"]);
            }

            $options[] = $option;
        }

        $subject = new SingleChoiceVariable("test", options: $options, dataPath: ['path']);
        $i = 0;
        foreach ($subject->getOptions() as $option) {
            self::assertSame($options[$i], $option);
            $i++;
        }
    }

    /**
     * @return iterable<non-empty-list<mixed>>
     */
    public static function invalidValueProvider(): iterable
    {
        yield [154, "154"];
        yield ['151234abc', "151234abc"];
        yield [15.3, "15.30"];
        yield [["Test array value"], print_r(["Test array value"], true)];
    }

    /**
     * @param int|string|array<mixed>|float $value
     */
    #[DataProvider('invalidValueProvider')]
    public function testGetInvalidValue(int|string|array|float $value, string $displayValue): void
    {
        $subject = new SingleChoiceVariable("test", options: [new IntegerValueOption(15, ['en' => 'test'])], dataPath: ['path']);

        $data = new ArrayDataRecord(['path' => $value]);

        $retrievedValue = $subject->getValue($data);

        self::assertInstanceOf(SpecialValueInterface::class, $retrievedValue);
        self::assertSame(ValueType::Invalid, $retrievedValue->getType());
        self::assertSame(StringValue::toString($value), $retrievedValue->getValue());
        self::assertSame($displayValue, $retrievedValue->getDisplayValue());
    }

    public function testGetValidValue(): void
    {
        $value = 15;
        $subject = new SingleChoiceVariable("test", options: [new IntegerValueOption($value, ['en' => 'test', 'de' => 'test2'])], dataPath: ['path']);

        $data = new ArrayRecord(['path' => $value], 5, new \DateTime(), new \DateTime());

        $retrievedValue = $subject->getValue($data);
        self::assertInstanceOf(IntegerValueOption::class, $retrievedValue);

        self::assertSame($value, $retrievedValue->getValue());

        self::assertSame('test', $subject->getValue($data)->getDisplayValue());
        self::assertSame('test2', $subject->getValue($data)->getDisplayValue('de'));
    }

    public function testGetInValidDisplayValue(): void
    {
        $value = 1213123132;
        $subject = new SingleChoiceVariable("test", options: [new IntegerValueOption(15, ['en' => 'test', 'de' => 'test2'])], dataPath: ['path']);

        $data = new ArrayRecord(['path' => $value], 5, new \DateTime(), new \DateTime());

        $value = $subject->getValue($data);
        self::assertInstanceOf(SpecialValueInterface::class, $value);
        self::assertSame(ValueType::Invalid, $value->getType());
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new SingleChoiceVariable("test", options: [new IntegerValueOption(15, ['en' => 'test'])], dataPath: ['path'], rawConfiguration: $rawConfiguration);
    }

    protected function getVariableWithName(string $name, array $dataPath = ['path']): SingleChoiceVariable
    {
        return new SingleChoiceVariable($name, options: [new IntegerValueOption(15, ['en' => 'test'])], dataPath: $dataPath);
    }

    /**
     * @param array<string,string> $titles
     */
    protected function getVariableWithTitles(array $titles): SingleChoiceVariable
    {
        return new SingleChoiceVariable(
            'test',
            options: [new IntegerValueOption(15, ['en' => 'test'])],
            dataPath: ['path'],
            titles: $titles
        );
    }
}
