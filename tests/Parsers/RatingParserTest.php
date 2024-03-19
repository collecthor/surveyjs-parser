<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\RatingParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceIntegerVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(RatingParser::class)]
final class RatingParserTest extends TestCase
{
    public function testDefaultRating(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            'type' => 'rating',
            'name' => 'question3',
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(SingleChoiceIntegerVariable::class, $result[0]);

        $variable = $result[0];

        $choices = $variable->getOptions();
        self::assertCount(5, $choices);
        self::assertContainsOnlyInstancesOf(IntegerValueOption::class, $choices);

        $values = array_map(function (IntegerValueOption $option) {
            return $option->getValue();
        }, $choices);
        self::assertSame([1, 2, 3, 4, 5], $values);
    }

    public function testCustomRange(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            'type' => 'rating',
            'name' => 'question3',
            'rateMin' => 2,
            'rateMax' => 10,
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(SingleChoiceIntegerVariable::class, $result[0]);

        $variable = $result[0];

        $choices = $variable->getOptions();
        self::assertCount(9, $choices);
        self::assertContainsOnlyInstancesOf(IntegerValueOption::class, $choices);

        for ($i = 2; $i <= 10; $i++) {
            self::assertSame($i, $choices[$i - 2]->getValue());
        }
    }

    public function testCustomStep(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            'type' => 'rating',
            'name' => 'question3',
            'rateMin' => 2,
            'rateMax' => 10,
            'rateStep' => 3,
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        $variable = $result[0];
        self::assertInstanceOf(SingleChoiceIntegerVariable::class, $variable);


        $choices = $variable->getOptions();

        self::assertCount(3, $choices);
        self::assertContainsOnlyInstancesOf(IntegerValueOption::class, $choices);
        self::assertSame(2, $choices[0]->getValue());
        self::assertSame(5, $choices[1]->getValue());
        self::assertSame(8, $choices[2]->getValue());
    }

    public function testCustomRangeRating(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "rating",
            "name" => "question3",
            "rateValues" => [
                [
                    "value" => "item1",
                    "text" => "Bad"
                ],
                [
                    "value" => "item2",
                    "text" => "Medium"
                ],
                [
                    "value" => "item3",
                    "text" => "Good"
                ],
                [
                    "value" => "item4",
                    "text" => "Excellent"
                ]
            ],
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(SingleChoiceVariable::class, $result[0]);
    }

    public function testCustomRangeRatingAnswers(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "rating",
            "name" => "question3",
            "rateValues" => [
                [
                    "value" => "item1",
                    "text" => "Bad"
                ],
                [
                    "value" => "item2",
                    "text" => "Medium"
                ],
                [
                    "value" => "item3",
                    "text" => "Good"
                ],
                [
                    "value" => "item4",
                    "text" => "Excellent"
                ]
            ],
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $result);

        $record = new ArrayDataRecord(['question3' => 'item3']);

        $value = $result->getValue($record);

        self::assertInstanceOf(ValueOptionInterface::class, $value);


        self::assertSame('item3', $value->getValue());

        self::assertSame('Good', $value->getDisplayValue());
    }

    public function testCustomRangeRatingAnswersLocalized(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "rating",
            "name" => "question3",
            "rateValues" => [
                [
                    "value" => "item1",
                    "text" => [
                        "default" => "Bad",
                        "nl" => "Slecht"
                    ]
                ],
                [
                    "value" => "item2",
                    "text" => [
                        "default" => "Medium",
                        "nl" => "Matig"
                    ]
                ],
                [
                    "value" => "item3",
                    "text" => [
                        "default" => "Good",
                        "nl" => "Goed"
                    ]
                ],
                [
                    "value" => "item4",
                    "text" => [
                        "default" => "Excellent",
                        "nl" => "Zeer goed"
                    ]
                ]
            ],
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];

        self::assertInstanceOf(VariableInterface::class, $result);
        $record = new ArrayDataRecord(['question3' => 'item3']);

        $displayValue = $result->getValue($record)->getDisplayValue('nl');


        self::assertSame('Goed', $displayValue);
    }
}
