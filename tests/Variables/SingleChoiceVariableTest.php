<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\DataInterfaces\InvalidValueInterface;
use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\Values\InvalidValue
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 */
class SingleChoiceVariableTest extends VariableTest
{
    public function testMeasureIsNominal(): void
    {
        $option = new IntegerValueOption(15, []);
        $subject = new SingleChoiceVariable("test", [], [$option], ['path']);
        self::assertSame(Measure::Nominal, $subject->getMeasure());
    }

    public function testGetValueOptions(): void
    {
        /**
         * @todo Remove this
         * @see https://github.com/phpstan/phpstan/issues/6070
         * @phpstan-var non-empty-list<ValueOptionInterface> $options
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


        $subject = new SingleChoiceVariable("test", [], $options, ['path']);
        $i = 0;
        foreach ($subject->getValueOptions() as $option) {
            self::assertSame($options[$i], $option);
            $i++;
        }
    }

    /**
     * @return iterable<non-empty-list<mixed>>
     */
    public function invalidValueProvider(): iterable
    {
        yield [154];
        yield ['151234abc'];
        yield [15.3];
        yield [["Test array value"]];
    }

    /**
     * @dataProvider invalidValueProvider
     * @phpstan-param int|string|array<mixed>|float $value
     */
    public function testGetInvalidValue(int|string|array|float $value): void
    {
        $subject = new SingleChoiceVariable("test", [], [new IntegerValueOption(15, ['en' => 'test'])], ['path']);

        $data = new ArrayRecord(['path' => $value], 5, new \DateTime(), new \DateTime());

        $retrievedValue = $subject->getValue($data);
        self::assertInstanceOf(InvalidValueInterface::class, $retrievedValue);

        /**
         * @see https://github.com/collecthor/surveyjs-parser/issues/2
         */
        if (!is_array($value)) {
            self::assertSame((string) $value, $retrievedValue->getRawValue());
        }
    }

    public function testGetValidValue(): void
    {
        $value = 15;
        $subject = new SingleChoiceVariable("test", [], [new IntegerValueOption($value, ['en' => 'test', 'de' => 'test2'])], ['path']);

        $data = new ArrayRecord(['path' => $value], 5, new \DateTime(), new \DateTime());

        $retrievedValue = $subject->getValue($data);
        self::assertInstanceOf(IntegerValueOption::class, $retrievedValue);

        self::assertSame($value, $retrievedValue->getRawValue());

        self::assertSame('test', $subject->getDisplayValue($data)->getRawValue());
        self::assertSame('test2', $subject->getDisplayValue($data, 'de')->getRawValue());
    }

    public function testGetInValidDisplayValue(): void
    {
        $value = 1213123132;
        $subject = new SingleChoiceVariable("test", [], [new IntegerValueOption(15, ['en' => 'test', 'de' => 'test2'])], ['path']);

        $data = new ArrayRecord(['path' => $value], 5, new \DateTime(), new \DateTime());

        $displayValue = $subject->getDisplayValue($data);

        self::assertSame((string) $value, $displayValue->getRawValue());
        self::assertInstanceOf(InvalidValueInterface::class, $displayValue);
    }

    protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface
    {
        return new SingleChoiceVariable("test", [], [new IntegerValueOption(15, ['en' => 'test'])], ['path'], $rawConfiguration);
    }

    protected function getVariableWithName(string $name): JavascriptVariableInterface
    {
        return new SingleChoiceVariable($name, [], [new IntegerValueOption(15, ['en' => 'test'])], ['path']);
    }

    protected function getVariableWithTitles(array $titles): VariableInterface
    {
        return new SingleChoiceVariable('test', $titles, [new IntegerValueOption(15, ['en' => 'test'])], ['path']);
    }
}
