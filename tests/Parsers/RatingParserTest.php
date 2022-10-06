<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\RatingParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\RatingParser
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Variables\NumericVariable
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 *
 */

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

        self::assertInstanceOf(SingleChoiceVariable::class, $result[0]);

        /** @var SingleChoiceVariable $variable */
        $variable = $result[0];

        $choices = $variable->getValueOptions();

        for ($i = 1; $i < 6; $i++) {
            self::assertSame($i, $choices[$i - 1]->getRawValue());
            self::assertInstanceOf(IntegerValueOption::class, $choices[$i - 1]);
        }
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

        self::assertInstanceOf(SingleChoiceVariable::class, $result[0]);

        /** @var SingleChoiceVariable $variable */
        $variable = $result[0];

        $choices = $variable->getValueOptions();

        for ($i = 2; $i <= 10; $i++) {
            self::assertSame($i, $choices[$i - 2]->getRawValue());
            self::assertInstanceOf(IntegerValueOption::class, $choices[$i - 2]);
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

        self::assertInstanceOf(SingleChoiceVariable::class, $result[0]);

        /** @var SingleChoiceVariable $variable */
        $variable = $result[0];

        $choices = $variable->getValueOptions();

        self::assertCount(3, $choices);
        self::assertSame(2, $choices[0]->getRawValue());
        self::assertSame(5, $choices[1]->getRawValue());
        self::assertSame(8, $choices[2]->getRawValue());

        foreach ($choices as $choice) {
            self::assertInstanceOf(IntegerValueOption::class, $choice);
        }
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

        /** @var SingleChoiceVariable $result */
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];

        $record = new ArrayRecord(['question3' => 'item3'], 1, new \DateTime(), new \DateTime());

        $value = $result->getValue($record)->getRawValue();

        self::assertSame('item3', $value);

        $displayValue = $result->getDisplayValue($record)->getRawValue();

        self::assertSame('Good', $displayValue);
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

        /** @var SingleChoiceVariable $result */
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];

        $record = new ArrayRecord(['question3' => 'item3'], 1, new \DateTime(), new \DateTime());

        $displayValue = $result->getDisplayValue($record, 'nl')->getRawValue();

        self::assertSame('Goed', $displayValue);
    }
}
