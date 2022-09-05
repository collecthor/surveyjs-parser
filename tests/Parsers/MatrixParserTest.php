<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MatrixParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\MatrixParser
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 */

final class MatrixParserTest extends TestCase
{
    public function testVariableCount(): void
    {
        $surveyConfig = new SurveyConfiguration(locales: ['default', 'nl']);
        $questionConfig = [
            "type" => "matrix",
            "name" => "question4",
            "columns" => [
                "Column 1",
                "Column 2",
                "Column 3"
            ],
            "rows" => [
                "Row 1",
                "Row 2",
                "Row 3",
                "Row 4",
                "Row 5"
            ]
        ];

        $parser = new MatrixParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(5, $result);

        foreach ($result as $variable) {
            self::assertInstanceOf(SingleChoiceVariable::class, $variable);
        }

        self::assertEquals("question4 - Row 1", $result[0]->getTitle());
    }

    public function testVariableNames(): void
    {
        $surveyConfig = new SurveyConfiguration(locales: ['default', 'nl']);
        $questionConfig = [
            "type" => "matrix",
            "name" => "question4",
            "columns" => [
                "Column 1",
                "Column 2",
                "Column 3"
            ],
            "rows" => [
                "Row 1",
                "Row 2",
                "Row 3",
                "Row 4",
                "Row 5"
            ]
        ];

        $parser = new MatrixParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertEquals("question4 - Row 1", $result[0]->getTitle());
        self::assertEquals("question4 - Row 2", $result[1]->getTitle());
        self::assertEquals("question4 - Row 3", $result[2]->getTitle());
        self::assertEquals("question4 - Row 4", $result[3]->getTitle());
        self::assertEquals("question4 - Row 5", $result[4]->getTitle());
    }

    public function testVariableOptions(): void
    {
        $surveyConfig = new SurveyConfiguration(locales: ['default', 'nl']);
        $questionConfig = [
            "type" => "matrix",
            "name" => "question4",
            "columns" => [
                "Column 1",
                "Column 2",
                "Column 3"
            ],
            "rows" => [
                "Row 1",
                "Row 2",
                "Row 3",
                "Row 4",
                "Row 5"
            ]
        ];

        $parser = new MatrixParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertEquals("question4 - Row 1", $result[0]->getTitle());

        $options = $result[0]->getValueOptions();

        self::assertEquals("Column 1", $options[0]->getRawValue());
        self::assertEquals("Column 1", $options[0]->getDisplayValue());
        self::assertEquals("Column 2", $options[1]->getRawValue());
        self::assertEquals("Column 2", $options[1]->getDisplayValue());
        self::assertEquals("Column 3", $options[2]->getRawValue());
        self::assertEquals("Column 3", $options[2]->getDisplayValue());
    }

    public function testExtractNames(): void
    {
        $surveyConfig = new SurveyConfiguration(locales: ['nl', 'default']);
        $questionConfig = [
            "type" => "matrix",
            "name" => "question4",
            "columns" => [
                [
                    "value" => "Column 1",
                    "text" => "Bad"
                ],
                [
                    "value" => "Column 2",
                    "text" => "Okay"
                ],
                [
                    "value" => "Column 3",
                    "text" => "Good"
                ]
            ],
            "rows" => [
                [
                    "value" => "Row 1",
                    "text" => "First choice"
                ],
                [
                    "value" => "Row 2",
                    "text" => "Second choice"
                ],
                [
                    "value" => "Row 3",
                    "text" => "Third choice"
                ],
                [
                    "value" => "Row 4",
                    "text" => "Fourth choice"
                ],
                [
                    "value" => "Row 5",
                    "text" => "Fifth choice"
                ]
            ]
        ];

        $parser = new MatrixParser();

        /** @var list<SingleChoiceVariable> $result */
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertSame('question4 - First choice', $result[0]->getTitle());
        self::assertSame('Bad', $result[0]->getValueOptions()[0]->getDisplayValue());
    }
}
