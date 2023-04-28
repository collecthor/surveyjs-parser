<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\DataInterfaces\ValueInterface;
use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceMatrixParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use DateTime;
use PHPUnit\Framework\TestCase;
use ValueError;
use function iter\toArray;

/**
 * @covers  \Collecthor\SurveyjsParser\Parsers\MultipleChoiceMatrixParser
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Parsers\TextQuestionParser
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\ValueSet
 * @uses \Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Values\NoneValueOption
 * @uses \Collecthor\SurveyjsParser\Values\OtherValueOption
 */
final class MultipleChoiceMatrixParserTest extends TestCase
{
    private function getRootParser(): ElementParserInterface
    {
        return new class() implements ElementParserInterface {
            public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
            {
                $dummyParser = new DummyParser();
                $singleChoiceParser = new SingleChoiceQuestionParser();
                $multipleChoiceParser = new MultipleChoiceQuestionParser();
                $textParser = new TextQuestionParser();
                if ($questionConfig['type'] === 'checkbox') {
                    yield from $multipleChoiceParser->parse($dummyParser, $questionConfig, $surveyConfiguration, $dataPrefix);
                } elseif ($questionConfig['type'] === 'dropdown') {
                    yield from $singleChoiceParser->parse($dummyParser, $questionConfig, $surveyConfiguration, $dataPrefix);
                } elseif ($questionConfig['type'] === 'comment') {
                    yield from $textParser->parse($dummyParser, $questionConfig, $surveyConfiguration, $dataPrefix);
                } else {
                    yield from $dummyParser->parse($dummyParser, $questionConfig, $surveyConfiguration, $dataPrefix);
                }
            }
        };
    }
    public function testParseMultipleChoiceMatrix(): void
    {
        $rootParser = $this->getRootParser();
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdropdown",
            "name" => "question1",
            "isRequired" => true,
            "columns" => [
                [
                    "name" => "col1",
                    "cellType" => "checkbox",
                    "isRequired" => true,
                    "showInMultipleColumns" => true,
                    "choices" => [
                        [
                            "value" => "1",
                            "text" => "Pakketten"
                        ],
                        [
                            "value" => "2",
                            "text" => "Pallets"
                        ],
                        [
                            "value" => "3",
                            "text" => "Rolcontainers"
                        ]
                    ],
                    "showNoneItem" => true,
                    "noneText" => "Maak ik geen gebruik van"
                ],
                [
                    "name" => "col2",
                    "cellType" => "dropdown",
                    "isRequired" => true,
                    "choices" => [
                        "item1",
                        "item2",
                        "item3"
                    ]
                ]
            ],
            "choices" => [
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "cellType" => "radiogroup",
            "rows" => [
                [
                    "value" => "1",
                    "text" => "DPD"
                ],
                [
                    "value" => "2",
                    "text" => "Transmission"
                ],
                [
                    "value" => "3",
                    "text" => "PostNL"
                ],
                [
                    "value" => "4",
                    "text" => "TNT Express"
                ],
                [
                    "value" => "5",
                    "text" => "UPS"
                ],
                [
                    "value" => "6",
                    "text" => "DHL"
                ],
                [
                    "value" => "7",
                    "text" => "GLS"
                ],
                [
                    "value" => "8",
                    "text" => "FedEx"
                ]
            ]
        ];
        $parser = new MultipleChoiceMatrixParser();
        $result = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));

        self::assertCount(16, $result);

        self::assertEquals("question1 - DPD - col1", $result[0]->getTitle());
        self::assertEquals("question1 - DPD - col2", $result[1]->getTitle());
        self::assertEquals("question1.1.col1", $result[0]->getName());
        self::assertEquals("question1.1.col2", $result[1]->getName());
    }

    public function testParsedVariables(): void
    {
        $rootParser = $this->getRootParser();
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdropdown",
            "name" => "question1",
            "isRequired" => true,
            "columns" => [
                [
                    "name" => "col1",
                    "cellType" => "checkbox",
                    "isRequired" => true,
                    "showInMultipleColumns" => true,
                    "choices" => [
                        [
                            "value" => "1",
                            "text" => "Pakketten"
                        ],
                        [
                            "value" => "2",
                            "text" => "Pallets"
                        ],
                        [
                            "value" => "3",
                            "text" => "Rolcontainers"
                        ]
                    ],
                    "showNoneItem" => true,
                    "noneText" => "Maak ik geen gebruik van"
                ],
                [
                    "name" => "col2",
                    "cellType" => "dropdown",
                    "isRequired" => true,
                    "choices" => [
                        "item1",
                        "item2",
                        "item3"
                    ]
                ]
            ],
            "choices" => [
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "cellType" => "radiogroup",
            "rows" => [
                [
                    "value" => "1",
                    "text" => "DPD"
                ],
                [
                    "value" => "2",
                    "text" => "Transmission"
                ],
                [
                    "value" => "3",
                    "text" => "PostNL"
                ],
                [
                    "value" => "4",
                    "text" => "TNT Express"
                ],
                [
                    "value" => "5",
                    "text" => "UPS"
                ],
                [
                    "value" => "6",
                    "text" => "DHL"
                ],
                [
                    "value" => "7",
                    "text" => "GLS"
                ],
                [
                    "value" => "8",
                    "text" => "FedEx"
                ]
            ]
        ];
        $parser = new MultipleChoiceMatrixParser();
        $result = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));

        self::assertCount(16, $result);

        self::assertInstanceOf(MultipleChoiceVariable::class, $result[0]);
        self::assertInstanceOf(SingleChoiceVariable::class, $result[1]);

        $valueOptions = $result[0]->getValueOptions();
        self::assertInstanceOf(StringValueOption::class, $valueOptions[0]);
        self::assertInstanceOf(StringValueOption::class, $valueOptions[1]);
        self::assertInstanceOf(StringValueOption::class, $valueOptions[2]);
        self::assertInstanceOf(NoneValueOption::class, $valueOptions[3]);

        self::assertEquals("Pakketten", $valueOptions[0]->getDisplayValue());
        self::assertEquals("1", $valueOptions[0]->getRawValue());
    }

    public function testValuePath(): void
    {
        $rootParser = $this->getRootParser();
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "matrixdropdown",
            "name" => "question1",
            "isRequired" => true,
            "columns" => [
                [
                    "name" => "col1",
                    "cellType" => "checkbox",
                    "isRequired" => true,
                    "showInMultipleColumns" => true,
                    "choices" => [
                        [
                            "value" => "1",
                            "text" => "Pakketten"
                        ],
                        [
                            "value" => "2",
                            "text" => "Pallets"
                        ],
                        [
                            "value" => "3",
                            "text" => "Rolcontainers"
                        ]
                    ],
                    "showNoneItem" => true,
                    "noneText" => "Maak ik geen gebruik van"
                ],
                [
                    "name" => "col2",
                    "cellType" => "dropdown",
                    "isRequired" => true,
                    "choices" => [
                        "item1",
                        "item2",
                        "item3"
                    ]
                ]
            ],
            "choices" => [
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "cellType" => "radiogroup",
            "rows" => [
                [
                    "value" => "1",
                    "text" => "DPD"
                ],
                [
                    "value" => "2",
                    "text" => "Transmission"
                ],
                [
                    "value" => "3",
                    "text" => "PostNL"
                ],
                [
                    "value" => "4",
                    "text" => "TNT Express"
                ],
                [
                    "value" => "5",
                    "text" => "UPS"
                ],
                [
                    "value" => "6",
                    "text" => "DHL"
                ],
                [
                    "value" => "7",
                    "text" => "GLS"
                ],
                [
                    "value" => "8",
                    "text" => "FedEx"
                ]
            ]
        ];
        $parser = new MultipleChoiceMatrixParser();
        $result = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));

        $record = new ArrayRecord([
            "language" => "en",
            "question1" => [
                "1" => [
                    "col1" => [
                        "none"
                    ],
                    "col2" => "item2"
                ],
                "2" => [
                    "col1" => [
                        "1", "2"
                    ]
                ],
                "3" => [
                    "col1" => [
                        "2"
                    ],
                    "col2" => "item3"
                ],
                "4" => [
                    "col1" => [
                        "3"
                    ]
                ],
                "5" => [
                    "col2" => "item2"
                ],
                "6" => [
                    "col1" => [
                        "none"
                    ]
                ],
                "7" => [
                    "col1" => [
                        "2"
                    ],
                    "col2" => "item2"
                ]
            ]
        ], 1, new DateTime(), new DateTime());


        $value = $result[0]->getValue($record);
        if ($value instanceof ValueInterface) {
            throw new ValueError("Value should be a ValueSetInterface");
        }
        $answers = $value->getValues();
        self::assertCount(1, $answers);
        self::assertEquals("none", $answers[0]->getRawValue());
        self::assertEquals("Maak ik geen gebruik van", $answers[0]->getDisplayValue());

        $value = $result[2]->getValue($record);
        if ($value instanceof ValueInterface) {
            throw new ValueError("Value should be a ValueSetInterface");
        }
        $answers = $value->getValues();
        self::assertEquals("Pakketten", $answers[0]->getDisplayValue());
        self::assertEquals("Pallets", $answers[1]->getDisplayValue());
    }


    public function testNoCellType(): void
    {
        $config = json_decode(<<<JSON
            {
             "type": "matrixdropdown",
             "name": "question2",
             "columns": [
              {
               "name": "Column 1"
              },
              {
               "name": "Column 2"
              },
              {
               "name": "Column 3"
              }
             ],
             "choices": [
              1,
              2,
              3,
              4,
              5
             ],
             "rows": [
              "Row 1",
              "Row 2"
             ]
            }
        JSON, true);
        $parser = new MultipleChoiceMatrixParser();
        $surveyConfig = new SurveyConfiguration();
        /**
         * @var list<MultipleChoiceVariable> $result
         */
        $result = toArray($parser->parse($this->getRootParser(), $config, $surveyConfig, []));

        self::assertCount(6, $result);
        foreach ($result as $variable) {
            self::assertCount(5, $variable->getValueOptions());
        }
    }

    public function testOnlyQuestionCellType(): void
    {
        $config = json_decode(<<<JSON
            {
             "type": "matrixdropdown",
             "name": "question2",
             "cellType": "comment",
             "columns": [
              {
               "name": "Column 1"
              },
              {
               "name": "Column 2"
              },
              {
               "name": "Column 3"
              }
             ],
             "choices": [
              1,
              2,
              3,
              4,
              5
             ],
             "rows": [
              "Row 1",
              "Row 2"
             ]
            }
        JSON, true);
        $parser = new MultipleChoiceMatrixParser();
        $surveyConfig = new SurveyConfiguration();
        /**
         * @var list<MultipleChoiceVariable> $result
         */
        $result = toArray($parser->parse($this->getRootParser(), $config, $surveyConfig, []));

        self::assertCount(6, $result);
        foreach ($result as $variable) {
            self::assertInstanceOf(OpenTextVariable::class, $variable);
        }
    }
}
