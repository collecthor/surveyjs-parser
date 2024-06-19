<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\SurveyParser;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use Collecthor\SurveyjsParser\VariableSet;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(SurveyParser::class)]
class SurveyParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-list<mixed>>
     */
    public static function sampleProvider(): iterable
    {
        $files = glob(__DIR__ . '/support/samples/*.json');
        foreach (is_array($files) ? $files : [] as $fileName) {
            $contents = file_get_contents($fileName);
            if (is_string($contents)) {
                yield [$contents];
            }
        }
    }

    #[DataProvider('sampleProvider')]
    public function testSamples(string $surveyJson): void
    {
        $parser = new SurveyParser();
        $variableSet = $parser->parseJson($surveyJson);
        self::assertInstanceOf(VariableSet::class, $variableSet);
        /** @var array{testConfig?:array{variableCount: int, samples?: list<array{data: array<string, mixed>, assertions: list<array{expected: mixed, variable: string}>}>}} $surveyConfig */
        $surveyConfig = json_decode($surveyJson, true);
        if (isset($surveyConfig['testConfig'])) {
            $testConfig = $surveyConfig['testConfig'];
            self::assertCount($testConfig['variableCount'], [...$variableSet->getVariables()]);
            if (isset($testConfig['samples'])) {
                foreach ($testConfig['samples'] as $sample) {
                    $data = new ArrayDataRecord($sample['data']);
                    foreach ($sample['assertions'] as $assertion) {
                        $value = $variableSet->getVariable($assertion['variable'])->getValue($data);
                        self::assertSame(
                            $assertion['expected'],
                            $value->getValue()
                        );
                    }
                }
            }
        }
    }

    public function testInvalidJsonData(): void
    {
        $parser = new SurveyParser();
        self::expectException(\InvalidArgumentException::class);
        $parser->parseJson('[1 ,5]');
    }

    public function testParseEmptySurvey(): void
    {
        $parser = new SurveyParser();
        $set = $parser->parseSurveyStructure([
            'commentPrefix' => 'thisisapostfix',
        ]);
        self::assertCount(0, toArray($set->getVariables()));
    }

    public function testParsePageWithElement(): void
    {
        $parser = new SurveyParser();
        $set = $parser->parseSurveyStructure([
            'commentPrefix' => 'thisisapostfix',
            'pages' => [
                [
                    'name' => 'test',
                    'elements' => [
                        [
                            'type' => 'text',
                            'title' => 'question text',
                            'name' => 'question1'
                        ]
                    ]
                ]
            ]
        ]);

        self::assertCount(1, toArray($set->getVariables()));
        $variable = $set->getVariable('question1');

        self::assertSame('question text', $variable->getTitle());
    }

    public function testCalculatedValues(): void
    {
        $parser = new SurveyParser();
        $result = $parser->parseSurveyStructure([
            'pages' => [
                [
                    'name' => 'test1',
                    'elements' => []
                ]
            ],
            'calculatedValues' => [
                [
                    'name' => 'CalculatedValue',
                    'expression' => '{question3}+{question5}',
                    'includeIntoResult' => true,
                ],
            ],
        ]);

        $variables = toArray($result->getVariables());

        self::assertCount(1, $variables);
        self::assertInstanceOf(OpenTextVariable::class, $variables[0]);
        self::assertSame('CalculatedValue', $variables[0]->getTitle());
    }

    public function testOnlyParseIncludedValues(): void
    {
        $parser = new SurveyParser();
        $result = $parser->parseSurveyStructure([
            'pages' => [
                [
                    'name' => 'test1',
                    'elements' => []
                ]
            ],
            'calculatedValues' => [
                [
                    'name' => 'CalculatedValue',
                    'expression' => '{question3}+{question5}',
                    'includeIntoResult' => false,
                ],
            ],
        ]);

        $variables = toArray($result->getVariables());
        self::assertEmpty($variables);
    }

    public function testChoicesFromQuestion(): void
    {
        $parser = new SurveyParser();
        $questionConfig = [
            "logoPosition" => "right",
            "pages" => [
                [
                    "name" => "page1",
                    "elements" => [
                        [
                            "type" => "checkbox",
                            "name" => "question1",
                            "choices" => [
                                "item1",
                                "item2",
                                "item3"
                            ]
                        ],
                        [
                            "type" => "radiogroup",
                            "name" => "question2",
                            "choicesFromQuestion" => "question1"
                        ],
                        [
                            "type" => "panel",
                            "name" => "panel1",
                            "elements" => [
                                [
                                    "type" => "radiogroup",
                                    "name" => "question3",
                                    "choices" => [
                                        "item3.1",
                                        "item3.2",
                                        "item3.3"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "text",
                            "name" => "question4"
                        ],
                        [
                            "type" => "radiogroup",
                            "name" => "question5",
                            "choicesFromQuestion" => "question3"
                        ],
                        [
                            "type" => "checkbox",
                            "name" => "question6",
                            "choicesFromQuestion" => "question5",
                        ],
                    ],
                ],
            ],
        ];

        $variables = $parser->parseSurveyStructure($questionConfig);
        self::assertCount(6, toArray($variables->getVariables()));

        $question2 = $variables->getVariable('question2');
        self::assertInstanceOf(SingleChoiceVariable::class, $question2);

        $options = $question2->getOptions();
        self::assertCount(3, $options);
        self::assertSame('item1', $options[0]->getValue());
        self::assertSame('item2', $options[1]->getValue());
        self::assertSame('item3', $options[2]->getValue());

        $question5 = $variables->getVariable('question5');
        self::assertInstanceOf(SingleChoiceVariable::class, $question5);

        $options = $question5->getOptions();
        self::assertCount(3, $options);
        self::assertSame('item3.1', $options[0]->getValue());
        self::assertSame('item3.2', $options[1]->getValue());
        self::assertSame('item3.3', $options[2]->getValue());

        $question6 = $variables->getVariable('question6');
        self::assertInstanceOf(MultipleChoiceVariable::class, $question6);

        $options = $question6->getOptions();
        self::assertCount(3, $options);
        self::assertSame('item3.1', $options[0]->getValue());
        self::assertSame('item3.2', $options[1]->getValue());
        self::assertSame('item3.3', $options[2]->getValue());
    }

    public function testV2ResultEqualsV1Result(): void
    {
        $v2SurveyStructure = [
            "title" => "test",
            "logoPosition" => "right",
            "pages" => [
                [
                    "name" => "page1",
                    "elements" => [
                        [
                            "type" => "text",
                            "name" => "question1",
                            "title" => "Wat is je naam?",
                            "description" => "Ik wil graag weten wat je naam is"
                        ],
                        [
                            "type" => "radiogroup",
                            "name" => "question9",
                            "title" => "Als je een huishoudelijk apparaat zou zijn, wat zou je dan zijn?",
                            "choices" => [
                                [
                                    "value" => "1",
                                    "text" => "Droger"
                                ],
                                [
                                    "value" => "2",
                                    "text" => "Wasmachine"
                                ],
                                [
                                    "value" => "3",
                                    "text" => "Vaatwasser"
                                ]
                            ],
                            "showOtherItem" => true,
                            "showNoneItem" => true,
                            "noneText" => "Ik ben helemaal geen huishoudelijk apparaat, ik ben een mens",
                            "otherText" => "Anders, namelijk:"
                        ],
                        [
                            "type" => "checkbox",
                            "name" => "question2",
                            "title" => "Hoe veel vragen heb je aangevinkt?",
                            "choices" => [
                                [
                                    "value" => "item1",
                                    "text" => "1"
                                ],
                                [
                                    "value" => "item2",
                                    "text" => "2"
                                ],
                                [
                                    "value" => "item3",
                                    "text" => "3"
                                ]
                            ],
                            "showCommentArea" => true,
                        ],
                        [
                            "type" => "comment",
                            "name" => "question4",
                            "title" => "Geef hier heel veel tekst in"
                        ],
                        [
                            "type" => "matrix",
                            "name" => "question3",
                            "title" => "Hoe ga je naar je werk?",
                            "columns" => [
                                [
                                    "value" => "1",
                                    "text" => "Fiets"
                                ],
                                [
                                    "value" => "2",
                                    "text" => "Auto"
                                ],
                                [
                                    "value" => "3",
                                    "text" => "Bus"
                                ],
                                [
                                    "value" => "4",
                                    "text" => "Trein"
                                ]
                            ],
                            "rows" => [
                                [
                                    "value" => "1",
                                    "text" => "Maandag"
                                ],
                                [
                                    "value" => "2",
                                    "text" => "Dinsdag"
                                ],
                                [
                                    "value" => "3",
                                    "text" => "Woensdag"
                                ],
                                [
                                    "value" => "4",
                                    "text" => "Donderdag"
                                ],
                                [
                                    "value" => "5",
                                    "text" => "Vrijdag"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "calculatedValues" => [
                [
                    "name" => "Calculated",
                    "includeIntoResult" => true
                ]
            ],
            "storeOthersAsComment" => false
        ];

        $v1SurveyStructure = [
            "title" => "test",
            "logoPosition" => "right",
            "pages" => [
                [
                    "name" => "page1",
                    "elements" => [
                        [
                            "type" => "text",
                            "name" => "question1",
                            "title" => "Wat is je naam?",
                            "description" => "Ik wil graag weten wat je naam is"
                        ],
                        [
                            "type" => "radiogroup",
                            "name" => "question9",
                            "title" => "Als je een huishoudelijk apparaat zou zijn, wat zou je dan zijn?",
                            "choices" => [
                                [
                                    "value" => "1",
                                    "text" => "Droger"
                                ],
                                [
                                    "value" => "2",
                                    "text" => "Wasmachine"
                                ],
                                [
                                    "value" => "3",
                                    "text" => "Vaatwasser"
                                ]
                            ],
                            "hasNone" => true,
                            "hasOther" => true,
                            "noneText" => "Ik ben helemaal geen huishoudelijk apparaat, ik ben een mens",
                            "otherText" => "Anders, namelijk:"
                        ],
                        [
                            "type" => "checkbox",
                            "name" => "question2",
                            "title" => "Hoe veel vragen heb je aangevinkt?",
                            "choices" => [
                                [
                                    "value" => "item1",
                                    "text" => "1"
                                ],
                                [
                                    "value" => "item2",
                                    "text" => "2"
                                ],
                                [
                                    "value" => "item3",
                                    "text" => "3"
                                ]
                            ],
                            "hasComment" => true,
                        ],
                        [
                            "type" => "comment",
                            "name" => "question4",
                            "title" => "Geef hier heel veel tekst in"
                        ],
                        [
                            "type" => "matrix",
                            "name" => "question3",
                            "title" => "Hoe ga je naar je werk?",
                            "columns" => [
                                [
                                    "value" => "1",
                                    "text" => "Fiets"
                                ],
                                [
                                    "value" => "2",
                                    "text" => "Auto"
                                ],
                                [
                                    "value" => "3",
                                    "text" => "Bus"
                                ],
                                [
                                    "value" => "4",
                                    "text" => "Trein"
                                ]
                            ],
                            "rows" => [
                                [
                                    "value" => "1",
                                    "text" => "Maandag"
                                ],
                                [
                                    "value" => "2",
                                    "text" => "Dinsdag"
                                ],
                                [
                                    "value" => "3",
                                    "text" => "Woensdag"
                                ],
                                [
                                    "value" => "4",
                                    "text" => "Donderdag"
                                ],
                                [
                                    "value" => "5",
                                    "text" => "Vrijdag"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "calculatedValues" => [
                [
                    "name" => "Calculated",
                    "includeIntoResult" => true
                ]
            ],
            "storeOthersAsComment" => false
        ];

        $parser = new SurveyParser();

        $v1Parsed = toArray($parser->parseSurveyStructure($v1SurveyStructure)->getVariables());
        $v2Parsed = toArray($parser->parseSurveyStructure($v2SurveyStructure)->getVariables());

        self::assertSameSize($v1Parsed, $v2Parsed);

        for ($i = 0; $i < count($v1Parsed); $i++) {
            /** @var VariableInterface $var1 */
            $var1 = $v1Parsed[$i];
            /** @var VariableInterface $var2 */
            $var2 = $v2Parsed[$i];
            self::assertInstanceOf($var1::class, $var2);
            self::assertSame($var1->getName(), $var2->getName());
            self::assertSame($var1->getTitle(), $var2->getTitle());
            if ($var1 instanceof ClosedVariableInterface) {
                self::assertInstanceOf(ClosedVariableInterface::class, $var2);
                self::assertEquals($var1->getOptions(), $var2->getOptions());
            }
        }
    }
}
