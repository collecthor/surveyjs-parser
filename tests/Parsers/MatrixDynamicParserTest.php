<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser
 * @uses \Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 */
final class MatrixDynamicParserTest extends TestCase
{
    public function testGenerateAmountOfRows(): void
    {
        $surveyConfig = new SurveyConfiguration(locales: ['default', 'nl']);
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question3",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 2"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "text"
                ],
                [
                    "name" => "Column 4",
                    "cellType" => "text"
                ]
            ],
            "choices" => [
                1,
                2,
                3,
                4,
                5
            ]
        ];

        $parser = new MatrixDynamicParser([
            'default' => 'row',
            'nl' => 'rij',
        ]);

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(4 * 10, $result);

        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question3",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 2"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "text"
                ],
                [
                    "name" => "Column 4",
                    "cellType" => "text"
                ]
            ],
            "choices" => [
                1,
                2,
                3,
                4,
                5
            ],
            "maxRowCount" => 5,
        ];

        $parser = new MatrixDynamicParser([
            'default' => 'row',
            'nl' => 'rij',
        ]);

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(4 * 5, $result);
    }

    public function testGenerateRightQuestionTypes(): void
    {
        $surveyConfig = new SurveyConfiguration(locales: ['default', 'nl']);
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question3",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 2"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "text"
                ],
                [
                    "name" => "Column 4",
                    "cellType" => "text"
                ]
            ],
            "choices" => [
                1,
                2,
                3,
                4,
                5
            ]
        ];

        $parser = new MatrixDynamicParser([
            'default' => 'row',
        ]);

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(MultipleChoiceVariable::class, $result[0]);
        self::assertInstanceOf(SingleChoiceVariable::class, $result[11]);
        self::assertInstanceOf(OpenTextVariable::class, $result[31]);
    }
}
