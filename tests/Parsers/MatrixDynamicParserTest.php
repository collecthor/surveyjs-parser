<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\SurveyParser;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser
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

        $rootParser->expects(self::exactly(4))->method('parse')->with($rootParser);


        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    /**
     * @coversNothing
     */
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


        $rootParser = new SurveyParser();
        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);
        /**
         * @var array{0: SingleChoiceVariable<string>, 1: MultipleChoiceVariable<int>, 2: SingleChoiceVariable<int>} $parsed
         */
        $parsed = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));

        self::assertCount(3, $parsed);

        self::assertInstanceOf(SingleChoiceVariable::class, $parsed[0]);
        self::assertInstanceOf(MultipleChoiceVariable::class, $parsed[1]);
        self::assertInstanceOf(SingleChoiceVariable::class, $parsed[2]);

        self::assertCount(5, $parsed[0]->getValueOptions());
        self::assertCount(5, $parsed[1]->getValueOptions());
        self::assertCount(5, $parsed[2]->getValueOptions());

        self::assertSame('item1', $parsed[0]->getValueOptions()[0]->getValue());
        self::assertSame(1, $parsed[1]->getValueOptions()[0]->getValue());
        self::assertSame(1, $parsed[2]->getValueOptions()[0]->getValue());
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
        self::markTestSkipped();
        //        $surveyConfig = new SurveyConfiguration();
        //        $questionConfig = [
        //            "type" => "matrixdynamic",
        //            "name" => "question1",
        //            "columns" => [
        //                [
        //                    "name" => "Column 1",
        //                    "cellType" => "dropdown",
        //                    "colCount" => 0,
        //                    "choices" => [
        //                        [
        //                            "value" => "item1",
        //                            "text" => "One"
        //                        ],
        //                        [
        //                            "value" => "item2",
        //                            "text" => "Two"
        //                        ],
        //                        [
        //                            "value" => "item3",
        //                            "text" => "Three"
        //                        ],
        //                        [
        //                            "value" => "item4",
        //                            "text" => "Four"
        //                        ],
        //                        [
        //                            "value" => "item5",
        //                            "text" => "Five"
        //                        ]
        //                    ],
        //                    "storeOthersAsComment" => true
        //                ],
        //                [
        //                    "name" => "Column 2",
        //                    "cellType" => "checkbox"
        //                ],
        //                [
        //                    "name" => "Column 3",
        //                    "cellType" => "radiogroup"
        //                ],
        //                [
        //                    "name" => "Column 4",
        //                    "cellType" => "text"
        //                ]
        //            ],
        //            "choices" => [
        //                1,
        //                2,
        //                3,
        //                4,
        //                5
        //            ],
        //            "rowCount" => 1,
        //            "maxRowCount" => 1
        //        ];
        //
        //
        //        $rootParser = $this->createMock(ElementParserInterface::class);
        //
        //        foreach([
        //            [
        //                self::anything(),
        //                self::callback(function ($columnConfig) {
        //                    return $columnConfig['title']['default'] === 'question1 Column 1 Row 0';
        //                }),
        //                self::anything(),
        //                self::anything()
        //            ],
        //            [
        //                self::anything(),
        //                self::callback(function ($columnConfig) {
        //                    return $columnConfig['title']['default'] === 'question1 Column 2 Row 0';
        //                }),
        //                self::anything(),
        //                self::anything()
        //            ],
        //            [
        //                self::anything(),
        //                self::callback(function ($columnConfig) {
        //                    return $columnConfig['title']['default'] === 'question1 Column 3 Row 0';
        //                }),
        //                self::anything(),
        //                self::anything()
        //            ],
        //            [
        //                self::anything(),
        //                self::callback(function ($columnConfig) {
        //                    return $columnConfig['title']['default'] === 'question1 Column 4 Row 0';
        //                }),
        //                self::anything(),
        //                self::anything()
        //            ],
        //        ] as $params) {
        //            $rootParser->expects(self::once())->method('parse')->with($params);
        //        }
        //
        //        $parser = new MatrixDynamicParser([
        //            'default' => 'Row',
        //        ]);
        //        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testGenerateRightDataPaths(): void
    {
        self::markTestSkipped();
        //        $surveyConfig = new SurveyConfiguration();
        //        $questionConfig = [
        //            "type" => "matrixdynamic",
        //            "name" => "question1",
        //            "columns" => [
        //                [
        //                    "name" => "Column 1",
        //                    "cellType" => "dropdown",
        //                    "colCount" => 0,
        //                    "choices" => [
        //                        [
        //                            "value" => "item1",
        //                            "text" => "One"
        //                        ],
        //                        [
        //                            "value" => "item2",
        //                            "text" => "Two"
        //                        ],
        //                        [
        //                            "value" => "item3",
        //                            "text" => "Three"
        //                        ],
        //                        [
        //                            "value" => "item4",
        //                            "text" => "Four"
        //                        ],
        //                        [
        //                            "value" => "item5",
        //                            "text" => "Five"
        //                        ]
        //                    ],
        //                    "storeOthersAsComment" => true
        //                ],
        //                [
        //                    "name" => "Column 2",
        //                    "cellType" => "checkbox"
        //                ],
        //                [
        //                    "name" => "Column 3",
        //                    "cellType" => "radiogroup"
        //                ],
        //                [
        //                    "name" => "Column 4",
        //                    "cellType" => "text"
        //                ]
        //            ],
        //            "choices" => [
        //                1,
        //                2,
        //                3,
        //                4,
        //                5
        //            ],
        //            "rowCount" => 1,
        //            "maxRowCount" => 1
        //        ];
        //
        //
        //
        //        $rootParser = $this->createMock(ElementParserInterface::class);
        //
        //        foreach([
        //            [
        //                self::anything(),
        //                self::anything(),
        //                self::anything(),
        //                self::callback(function ($dataPath) {
        //                    return $dataPath === ['question1', '0'];
        //                }),
        //            ],
        //            [
        //                self::anything(),
        //                self::anything(),
        //                self::anything(),
        //                self::callback(function ($dataPath) {
        //                    return $dataPath === ['question1', '0'];
        //                }),
        //            ],
        //            [
        //                self::anything(),
        //                self::anything(),
        //                self::anything(),
        //                self::callback(function ($dataPath) {
        //                    return $dataPath === ['question1', '0'];
        //                }),
        //            ],
        //            [
        //                self::anything(),
        //                self::anything(),
        //                self::anything(),
        //                self::callback(function ($dataPath) {
        //                    return $dataPath === ['question1', '0'];
        //                }),
        //            ],
        //        ] as $params) {
        //            $rootParser->expects(self::once())->method('parse')->with($params);
        //        }
        //
        //        $parser = new MatrixDynamicParser([
        //            'default' => 'Row',
        //        ]);
        //        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testCallWithRightClasses(): void
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


        $types = [];
        $rootParser = $this->createMock(ElementParserInterface::class);
        $rootParser->expects(self::exactly(4))->method('parse')->willReturnCallback(function ($p, array $questionConfig) use (&$types) {
            $types[] = $questionConfig['type'];
            return [];
        });

        $parser = new MatrixDynamicParser([
            'default' => 'Row',
        ]);

        toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));

        self::assertSame(['dropdown', 'checkbox', 'radiogroup', 'text'], $types);
    }
}
