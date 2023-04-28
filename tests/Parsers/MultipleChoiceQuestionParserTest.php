<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Tests\support\RawConfigurationTests;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\OtherValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Values\NoneValueOption
 * @uses \Collecthor\SurveyjsParser\Values\OtherValueOption
 */

final class MultipleChoiceQuestionParserTest extends TestCase
{
    use RawConfigurationTests;
    public function testMultipleChoice(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();

        $parser = new MultipleChoiceQuestionParser();

        $questionConfig = [
            'choices' => [
                ['value' => 'a', 'text' => 'b'],
                'c',
                15,
                ['value' => 16, 'text' => 'abc']
            ],
            'name' => 'q1',
        ];

        
        $variable = toArray($parser->parse($parent, $questionConfig, $surveyConfiguration))[0];
        self::assertInstanceOf(MultipleChoiceVariable::class, $variable);
        self::assertCount(4, $variable->getValueOptions());

        $options = $variable->getValueOptions();

        self::assertSame('b', $options[0]->getDisplayValue());
        self::assertSame('c', $options[1]->getDisplayValue());
        self::assertSame('15', $options[2]->getDisplayValue());
        self::assertSame('abc', $options[3]->getDisplayValue());
    }

    public function testHasNoneOther(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();

        $parser = new MultipleChoiceQuestionParser();

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
        self::assertInstanceOf(MultipleChoiceVariable::class, $variable);
        self::assertCount(5, $variable->getValueOptions());

        $options = $variable->getValueOptions();

        self::assertInstanceOf(StringValueOption::class, $options[0]);
        self::assertInstanceOf(StringValueOption::class, $options[1]);
        self::assertInstanceOf(StringValueOption::class, $options[2]);
        self::assertInstanceOf(NoneValueOption::class, $options[3]);
        self::assertInstanceOf(OtherValueOption::class, $options[4]);
    }

    protected function getParser(): ElementParserInterface
    {
        return new MultipleChoiceQuestionParser();
    }

    /**
     * @return non-empty-list<non-empty-array<string, mixed>>
     */
    protected function validConfigs(): array
    {
        return [
            [
                'choices' => [
                    ['value' => 'a', 'text' => 'b'],
                    'c',
                    15,
                    ['value' => 16, 'text' => 'abc']
                ],
                'name' => 'q1',
            ],
            [
                'choices' => [
                    ['value' => 'a', 'text' => 'b'],
                    'c',
                    15,
                    ['value' => 16, 'text' => 'abc']
                ],
                'name' => 'q1',
                'hasComment' => true,
                'hasOther' => true,
            ]
        ];
    }
}
