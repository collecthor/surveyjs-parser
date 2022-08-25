<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\RatingParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\NumericVariable;
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
 */

class RatingParserTest extends TestCase
{
    public function testDefaultRating(): void
    {
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
        $questionConfig = [
            'type' => 'rating',
            'name' => 'question3',
        ];

        $parser = new RatingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(NumericVariable::class, $result[0]);
    }

    public function testCustomRangeRating(): void
    {
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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
