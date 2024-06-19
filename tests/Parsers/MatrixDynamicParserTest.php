<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\SurveyParser;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

#[CoversClass(MatrixDynamicParser::class)]
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

    #[CoversNothing]
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
        $parsed = toArray($parser->parse(root: $rootParser, questionConfig: $questionConfig, surveyConfiguration: $surveyConfig));

        self::assertCount(3, $parsed);

        [$q1, $q2, $q3] = $parsed;
        self::assertInstanceOf(SingleChoiceVariable::class, $q1);
        self::assertInstanceOf(MultipleChoiceVariable::class, $q2);
        self::assertInstanceOf(SingleChoiceVariable::class, $q3);

        self::assertCount(5, $q1->getOptions());
        self::assertCount(5, $q2->getOptions());
        self::assertCount(5, $q3->getOptions());

        self::assertSame('item1', $q1->getOptions()[0]->getValue());
        self::assertSame(1, $q2->getOptions()[0]->getValue());
        self::assertSame(1, $q3->getOptions()[0]->getValue());
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
