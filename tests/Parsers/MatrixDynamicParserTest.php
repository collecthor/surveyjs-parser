<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 */
final class MatrixDynamicParserTest extends TestCase
{
    public function testUseRootParser(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question1",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "dropdown",
                ],
                [
                    "name" => "Column 2",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "radiogroup"
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
            "rowCount" => 1,
            "maxRowCount" => 1
        ];


        $rootParser = $this->createMock(ElementParserInterface::class);

        $rootParser->expects(self::exactly(4))->method('parse')->withConsecutive(
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return isset($columnConfig['type']) && $columnConfig['type'] === 'dropdown';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return isset($columnConfig['type']) && $columnConfig['type'] === 'checkbox';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return isset($columnConfig['type']) && $columnConfig['type'] === 'radiogroup';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return isset($columnConfig['type']) && $columnConfig['type'] === 'text';
                }),
                self::anything(),
                self::anything()
            ],
        );

        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testUseCustomDropdownOptions(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question1",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "dropdown",
                    "colCount" => 0,
                    "choices" => [
                        [
                            "value" => "item1",
                            "text" => "One"
                        ],
                        [
                            "value" => "item2",
                            "text" => "Two"
                        ],
                        [
                            "value" => "item3",
                            "text" => "Three"
                        ],
                        [
                            "value" => "item4",
                            "text" => "Four"
                        ],
                        [
                            "value" => "item5",
                            "text" => "Five"
                        ]
                    ],
                    "storeOthersAsComment" => true
                ],
                [
                    "name" => "Column 2",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "radiogroup"
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
            "rowCount" => 1,
            "maxRowCount" => 1
        ];


        $rootParser = $this->createMock(ElementParserInterface::class);

        $rootParser->expects(self::exactly(4))->method('parse')->withConsecutive(
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['choices'][0]['text'] === 'One'
                    && $columnConfig['choices'][1]['text'] === 'Two'
                    && $columnConfig['choices'][2]['text'] === 'Three'
                    && $columnConfig['choices'][3]['text'] === 'Four'
                    && $columnConfig['choices'][4]['text'] === 'Five'
                    && $columnConfig['type'] === 'dropdown';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['choices'][0]['text'] === '1'
                    && $columnConfig['choices'][1]['text'] === '2'
                    && $columnConfig['choices'][2]['text'] === '3'
                    && $columnConfig['choices'][3]['text'] === '4'
                    && $columnConfig['choices'][4]['text'] === '5'
                    && $columnConfig['type'] === 'checkbox';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['choices'][0]['text'] === '1'
                    && $columnConfig['choices'][1]['text'] === '2'
                    && $columnConfig['choices'][2]['text'] === '3'
                    && $columnConfig['choices'][3]['text'] === '4'
                    && $columnConfig['choices'][4]['text'] === '5'
                    && $columnConfig['type'] === 'radiogroup';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return isset($columnConfig['type']) && $columnConfig['type'] === 'text';
                }),
                self::anything(),
                self::anything()
            ],
        );

        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testGenerateMaxRows(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question1",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "dropdown",
                ],
                [
                    "name" => "Column 2",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "radiogroup"
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
            "rowCount" => 1,
            "maxRowCount" => 10
        ];


        $rootParser = $this->createMock(ElementParserInterface::class);

        $rootParser->expects(self::exactly(40))->method('parse');

        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testGenerateRightTitles(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question1",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "dropdown",
                    "colCount" => 0,
                    "choices" => [
                        [
                            "value" => "item1",
                            "text" => "One"
                        ],
                        [
                            "value" => "item2",
                            "text" => "Two"
                        ],
                        [
                            "value" => "item3",
                            "text" => "Three"
                        ],
                        [
                            "value" => "item4",
                            "text" => "Four"
                        ],
                        [
                            "value" => "item5",
                            "text" => "Five"
                        ]
                    ],
                    "storeOthersAsComment" => true
                ],
                [
                    "name" => "Column 2",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "radiogroup"
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
            "rowCount" => 1,
            "maxRowCount" => 1
        ];


        $rootParser = $this->createMock(ElementParserInterface::class);

        $rootParser->expects(self::exactly(4))->method('parse')->withConsecutive(
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['name']['default'] === 'question1 Column 1 Row 0';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['name']['default'] === 'question1 Column 2 Row 0';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['name']['default'] === 'question1 Column 3 Row 0';
                }),
                self::anything(),
                self::anything()
            ],
            [
                self::anything(),
                self::callback(function ($columnConfig) {
                    return $columnConfig['name']['default'] === 'question1 Column 4 Row 0';
                }),
                self::anything(),
                self::anything()
            ],
        );

        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testGenerateRightDataPaths(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdynamic",
            "name" => "question1",
            "columns" => [
                [
                    "name" => "Column 1",
                    "cellType" => "dropdown",
                    "colCount" => 0,
                    "choices" => [
                        [
                            "value" => "item1",
                            "text" => "One"
                        ],
                        [
                            "value" => "item2",
                            "text" => "Two"
                        ],
                        [
                            "value" => "item3",
                            "text" => "Three"
                        ],
                        [
                            "value" => "item4",
                            "text" => "Four"
                        ],
                        [
                            "value" => "item5",
                            "text" => "Five"
                        ]
                    ],
                    "storeOthersAsComment" => true
                ],
                [
                    "name" => "Column 2",
                    "cellType" => "checkbox"
                ],
                [
                    "name" => "Column 3",
                    "cellType" => "radiogroup"
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
            "rowCount" => 1,
            "maxRowCount" => 1
        ];


        $rootParser = $this->createMock(ElementParserInterface::class);

        $rootParser->expects(self::exactly(4))->method('parse')->withConsecutive(
            [
                self::anything(),
                self::anything(),
                self::anything(),
                self::callback(function ($dataPath) {
                    return $dataPath === ['question1', 'Column 1', '0'];
                }),
            ],
            [
                self::anything(),
                self::anything(),
                self::anything(),
                self::callback(function ($dataPath) {
                    return $dataPath === ['question1', 'Column 2', '0'];
                }),
            ],
            [
                self::anything(),
                self::anything(),
                self::anything(),
                self::callback(function ($dataPath) {
                    return $dataPath === ['question1', 'Column 3', '0'];
                }),
            ],
            [
                self::anything(),
                self::anything(),
                self::anything(),
                self::callback(function ($dataPath) {
                    return $dataPath === ['question1', 'Column 4', '0'];
                }),
            ],
        );

        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }
}
