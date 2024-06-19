<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Tests\support\NameTests;
use Collecthor\SurveyjsParser\Tests\support\ValueNameTests;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\OtherValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(SingleChoiceQuestionParser::class)]
final class SingleChoiceQuestionParserTest extends TestCase
{
    use ValueNameTests;
    use NameTests;
    /**
     * @return iterable<list<array<string, mixed>>>
     */
    public static function badChoicesProvider(): iterable
    {
        yield [['text' => 'abc']];
        yield [['value' => 'abc', 'text' => 15]];
        yield [['value' => ['abc'], 'text' => 'ab4']];
    }

    /**
     * @param array<mixed> $choice
     */
    #[DataProvider('badChoicesProvider')]
    public function testBadChoices(array $choice): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();
        $parser = new SingleChoiceQuestionParser();

        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parent, [
            'choices' => [
                $choice
            ],
            'name' => 'q1',

        ], $surveyConfiguration));
    }


    public function testKeyValueChoices(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();
        $parser = new SingleChoiceQuestionParser();

        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parent, [
            'choices' => [
                'a' => 'b',
                'c' => 'd'
            ],
            'name' => 'q1',

        ], $surveyConfiguration));
    }


    public function testChoices(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();


        $parser = new SingleChoiceQuestionParser();

        $variable = toArray($parser->parse($parent, [
            'choices' => [
                ['value' => 'a', 'text' => 'b'],
                'c',
                15,
                ['value' => 16, 'text' => 'abc']
            ],
            'name' => 'q1',

        ], $surveyConfiguration))[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $variable);

        $options = $variable->getOptions();
        self::assertCount(4, $options);
        self::assertSame('a', $options[0]->getValue());
        self::assertSame('c', $options[1]->getValue());
        self::assertSame(15, $options[2]->getValue());
        self::assertSame(16, $options[3]->getValue());

        self::assertSame('b', $options[0]->getDisplayValue());
        self::assertSame('c', $options[1]->getDisplayValue());
        self::assertSame("15", $options[2]->getDisplayValue());
        self::assertSame('abc', $options[3]->getDisplayValue());
    }

    public function testHasNone(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();


        $parser = new SingleChoiceQuestionParser();
        $variable = toArray($parser->parse($parent, [
            'name' => 'test',
            'choices' => [
                'a'
            ],
            'hasNone' => true,
            'hasOther' => true,
        ], $surveyConfiguration))[0];

        self::assertInstanceOf(SingleChoiceVariable::class, $variable);
        $options = $variable->getOptions();
        self::assertCount(3, $options);
        $rawValues = [$options[0]->getValue(), $options[1]->getValue(), $options[2]->getValue()];
        self::assertSame(['a', 'none', 'other'], $rawValues);
    }

    public function testChoicesWrongType(): void
    {
        $parser = new SingleChoiceQuestionParser();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected to find an array at key choices");
        toArray($parser->parse(new DummyParser(), [
            'name' => 'test',
            'choices' => 15
        ], new SurveyConfiguration()));
    }

    public function testChoicesFromQuestionSimple(): void
    {
        $questionConfig =
            [
                'name' => 'question1',
                'choicesFromQuestion' => 'question2',
            ];
        $parser = $this->getParser();
        $question1 = toArray($parser->parse(new DummyParser(), $questionConfig, new SurveyConfiguration()))[0];

        self::assertInstanceOf(DeferredVariable::class, $question1);
        $valueOptions = [
            new StringValueOption('a', ['default' => 'a']),
            new StringValueOption('b', ['default' => 'b']),
            new StringValueOption('c', ['default' => 'c']),
        ];

        $question2 = new SingleChoiceVariable(
            name: 'question2',
            options: $valueOptions,
            dataPath: ['question2'],
            titles: ['default' => 'question2']
        );

        $resolvable = new ResolvableVariableSet($question1, $question2);

        $resolved = $question1->resolve($resolvable);

        self::assertInstanceOf(SingleChoiceVariable::class, $resolved);
        self::assertSame($valueOptions, $resolved->getOptions());
    }


    public function testParseNumericOptionsAsNumbers(): void
    {
        $questionConfig =
            [
                'name' => 'question1',
                'choices' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            ];
        $parser = $this->getParser();
        $question1 = toArray($parser->parse(new DummyParser(), $questionConfig, new SurveyConfiguration()))[0];

        self::assertInstanceOf(SingleChoiceVariable::class, $question1);

        self::assertContainsOnlyInstancesOf(IntegerValueOption::class, $question1->getOptions());
    }

    public function testParseOptionsThatContainNumbersButAreNotNumberNotAsNumbers(): void
    {
        $questionConfig =
            [
                'name' => 'question1',
                'choices' => [
                    '1a',
                    '2c',
                    '3f',
                    '4h',
                ],
            ];
        $parser = $this->getParser();
        $question1 = toArray($parser->parse(new DummyParser(), $questionConfig, new SurveyConfiguration()))[0];

        self::assertInstanceOf(SingleChoiceVariable::class, $question1);

        self::assertContainsOnlyInstancesOf(StringValueOption::class, $question1->getOptions());
    }

    public function testHasNoneOther(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();

        $parser = new SingleChoiceQuestionParser();

        $questionConfig = [
            "type" => "checkbox",
            "name" => "question5",
            "choices" => [
                [
                    "value" => "item1",
                    "text" => "1"
                ],
                [
                    "value" => "item2",
                    "text" => "2"
                ],
                [
                    "value" => "item3",
                    "text" => "3"
                ]
            ],
            "showOtherItem" => true,
            "showNoneItem" => true,
            "noneText" => "Geen",
            "otherText" => "Anders"
        ];

        $variable = toArray($parser->parse($parent, $questionConfig, $surveyConfiguration))[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $variable);
        $options = $variable->getOptions();
        self::assertCount(5, $options);
        self::assertInstanceOf(StringValueOption::class, $options[0]);
        self::assertInstanceOf(StringValueOption::class, $options[1]);
        self::assertInstanceOf(StringValueOption::class, $options[2]);
        self::assertInstanceOf(NoneValueOption::class, $options[3]);
        self::assertInstanceOf(OtherValueOption::class, $options[4]);
    }


    protected function getParser(): ElementParserInterface
    {
        return new SingleChoiceQuestionParser();
    }
}
