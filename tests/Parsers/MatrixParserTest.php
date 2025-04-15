<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MatrixParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function Collecthor\SurveyjsParser\Tests\support\assertContainsOnlyInstancesOfFixed;
use function iter\toArray;

#[CoversClass(MatrixParser::class)]
final class MatrixParserTest extends TestCase
{
    public function testVariableCount(): void
    {
        $surveyConfig = new SurveyConfiguration();
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

        assertContainsOnlyInstancesOfFixed(VariableInterface::class, $result);
        self::assertEquals("question4 - Row 1", $result[0]->getTitle());
    }

    public function testVariableNames(): void
    {
        $surveyConfig = new SurveyConfiguration();
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

        assertContainsOnlyInstancesOfFixed(VariableInterface::class, $result);
        self::assertEquals("question4 - Row 1", $result[0]->getTitle());
        self::assertEquals("question4 - Row 2", $result[1]->getTitle());
        self::assertEquals("question4 - Row 3", $result[2]->getTitle());
        self::assertEquals("question4 - Row 4", $result[3]->getTitle());
        self::assertEquals("question4 - Row 5", $result[4]->getTitle());
    }

    public function testVariableOptions(): void
    {
        $surveyConfig = new SurveyConfiguration();
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
        $variable = $result[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $variable);
        self::assertEquals("question4 - Row 1", $variable->getTitle());

        $options = $variable->getOptions();
        self::assertCount(3, $options);

        self::assertEquals("Column 1", $options[0]->getValue());
        self::assertEquals("Column 1", $options[0]->getDisplayValue());
        self::assertEquals("Column 2", $options[1]->getValue());
        self::assertEquals("Column 2", $options[1]->getDisplayValue());
        self::assertEquals("Column 3", $options[2]->getValue());
        self::assertEquals("Column 3", $options[2]->getDisplayValue());
    }

    public function testExtractNames(): void
    {
        $surveyConfig = new SurveyConfiguration();
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

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(5, $result);

        $variable = $result[0];
        self::assertInstanceOf(ClosedVariableInterface::class, $variable);
        $options = $variable->getOptions();
        self::assertSame('question4 - First choice', $variable->getTitle());
        self::assertSame('Bad', $options[0]->getDisplayValue());
    }
}
