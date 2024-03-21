<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Helpers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Helpers\FlattenResponseHelper;
use Collecthor\SurveyjsParser\Interfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Values\RefuseValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\IntegerVariable;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use Collecthor\SurveyjsParser\VariableSet;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

#[CoversClass(FlattenResponseHelper::class)]
final class FlattenResponseHelperTest extends TestCase
{
    /**
     * @param list<array<string, mixed>> $response
     * @param list<array<string, mixed>> $result
     */
    #[DataProvider('provider')]
    public function testFlattenResponse(VariableSetInterface $variables, array $response, string $locale, array $result): void
    {
        $helper = new FlattenResponseHelper($variables, $locale);
        $records = [];
        for ($i = 0; $i < count($response); $i++) {
            $records[] = new ArrayRecord($response[$i], $i, new \DateTime(), new \DateTime());
        }

        $flattened = toArray($helper->flattenAll($records));
        self::assertSame($result, $flattened);
    }

    /**
     * @return iterable<array{0: VariableSet, 1: list<array<string, mixed>>, 2: string, 3: list<array<string, mixed>>}>
     */
    public static function provider(): iterable
    {
        $openText = [
            new VariableSet(
                new OpenTextVariable(
                    name: 'question1',
                    dataPath: ['question1'],
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ],
                ),
            ),
            [
                ['question1' => 'test', ],
            ],
            'default',
            [
                ['question1' => 'test', ],
            ],
        ];
        yield $openText;
        $openText[2] = 'nl';
        $openText[3] = [
            ['vraag1' => 'test'],
        ];
        yield $openText;
        $numeric = [
            new VariableSet(
                new IntegerVariable(
                    'question1',
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ],
                    dataPath: ['question1'],
                ),
            ),
            [
                ['question1' => 6, ],
            ],
            'default',
            [
                ['question1' => '6', ],
            ],
        ];
        yield $numeric;
        $numeric[2] = 'nl';
        $numeric[3] = [
            ['vraag1' => '6'],
        ];
        yield $numeric;

        $multipleResponse = [
            new VariableSet(
                new OpenTextVariable(
                    'question1',
                    dataPath: ['question1'],
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ],
                ),
            ),
            [
                ['question1' => 'test', ],
                ['question1' => 'test 2', ],
            ],
            'default',
            [
                ['question1' => 'test', ],
                ['question1' => 'test 2', ],
            ],
        ];
        yield $multipleResponse;
        $multipleResponse[2] = 'nl';
        $multipleResponse[3] = [
            ['vraag1' => 'test'],
            ['vraag1' => 'test 2'],
        ];
        yield $multipleResponse;

        $options = [
            new StringValueOption('option1', [
                'default' => 'option 1',
                'nl' => 'optie 1',
            ]),
            new StringValueOption('option2', [
                'default' => 'option 2',
                'nl' => 'optie 2',
            ]),
            new StringValueOption('option3', [
                'default' => 'option 3',
                'nl' => 'optie 3',
            ]),
        ];
        $singleChoice = [
            new VariableSet(
                new SingleChoiceVariable(
                    name: 'question1',
                    options: $options,
                    dataPath: ['question1'],
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ]
                ),
            ),
            [
                ['question1' => 'option2', ],
            ],
            'default',
            [
                ['question1' => 'option 2', ],
            ],
        ];

        yield $singleChoice;
        $singleChoice[2] = 'nl';
        $singleChoice[3] = [
            ['vraag1' => 'optie 2'],
        ];
        yield $singleChoice;

        $multipleChoice = [
            new VariableSet(
                new MultipleChoiceVariable(
                    name: 'question1',
                    dataPath: ['question1'],
                    options: $options,
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ]
                ),
            ),
            [
                ['question1' => ['option2', 'option3'], ],
            ],
            'default',
            [
                [
                    'question1 - option 1' => 0,
                    'question1 - option 2' => 1,
                    'question1 - option 3' => 1,
                ]
            ],
        ];

        yield $multipleChoice;
        $multipleChoice[2] = 'nl';
        $multipleChoice[3] = [
            [
                'vraag1 - optie 1' => 0,
                'vraag1 - optie 2' => 1,
                'vraag1 - optie 3' => 1
            ],
        ];
        yield $multipleChoice;

        $multipleVariable = [
            new VariableSet(
                new OpenTextVariable(
                    'question1',
                    dataPath: ['question1'],
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ],
                ),
                new OpenTextVariable(
                    'question2',
                    dataPath: ['question2'],
                    titles: [
                        'default' => 'question2',
                        'nl' => 'vraag2',
                    ],
                ),
            ),
            [
                [
                    'question1' => 'test',
                    'question2' => 'test 2',
                ],
            ],
            'default',
            [
                [
                    'question1' => 'test',
                    'question2' => 'test 2',
                ],
            ],
        ];
        yield $multipleVariable;
        $multipleVariable[2] = 'nl';
        $multipleVariable[3] = [
            [
                'vraag1' => 'test',
                'vraag2' => 'test 2',
            ],
        ];
        yield $multipleVariable;

        $multipleVariableMultipleRecords = [
            new VariableSet(
                new OpenTextVariable(
                    'question1',
                    dataPath: ['question1'],
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ],
                ),
                new OpenTextVariable(
                    'question2',
                    dataPath: ['question2'],
                    titles: [
                        'default' => 'question2',
                        'nl' => 'vraag2',
                    ],
                ),
            ),
            [
                [
                    'question1' => 'test',
                    'question2' => 'test 2',
                ],
                [
                    'question1' => 'second answer',
                    'question2' => 'second answer 2',
                ],
            ],
            'default',
            [
                [
                    'question1' => 'test',
                    'question2' => 'test 2',
                ],
                [
                    'question1' => 'second answer',
                    'question2' => 'second answer 2',
                ],
            ],
        ];
        yield $multipleVariableMultipleRecords;
        $multipleVariableMultipleRecords[2] = 'nl';
        $multipleVariableMultipleRecords[3] = [
            [
                'vraag1' => 'test',
                'vraag2' => 'test 2',
            ],
            [
                'vraag1' => 'second answer',
                'vraag2' => 'second answer 2',
            ],
        ];
        yield $multipleVariableMultipleRecords;

        $multipleVariableMultipleRecordsMultipleChoice = [
            new VariableSet(
                new MultipleChoiceVariable(
                    name: 'question1',
                    dataPath: ['question1'],
                    options: $options,
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ]
                ),
                new MultipleChoiceVariable(
                    name: 'question2',
                    dataPath: ['question2'],
                    options: $options,
                    titles: [
                        'default' => 'question2',
                        'nl' => 'vraag2',
                    ]
                ),
            ),
            [
                [
                    'question1' => ['option1'],
                    'question2' => ['option1', 'option2', 'option3'],
                ],
                [
                    'question1' => [],
                    'question2' => ['option1', 'option3'],
                ],
            ],
            'default',
            [
                [
                    'question1 - option 1' => 1,
                    'question1 - option 2' => 0,
                    'question1 - option 3' => 0,
                    'question2 - option 1' => 1,
                    'question2 - option 2' => 1,
                    'question2 - option 3' => 1,
                ],
                [
                    'question1 - option 1' => 0,
                    'question1 - option 2' => 0,
                    'question1 - option 3' => 0,
                    'question2 - option 1' => 1,
                    'question2 - option 2' => 0,
                    'question2 - option 3' => 1,
                ],
            ],
        ];
        yield $multipleVariableMultipleRecordsMultipleChoice;
        $multipleVariableMultipleRecordsMultipleChoice[2] = 'nl';
        $multipleVariableMultipleRecordsMultipleChoice[3] = [
            [
                'vraag1 - optie 1' => 1,
                'vraag1 - optie 2' => 0,
                'vraag1 - optie 3' => 0,
                'vraag2 - optie 1' => 1,
                'vraag2 - optie 2' => 1,
                'vraag2 - optie 3' => 1,
            ],
            [
                'vraag1 - optie 1' => 0,
                'vraag1 - optie 2' => 0,
                'vraag1 - optie 3' => 0,
                'vraag2 - optie 1' => 1,
                'vraag2 - optie 2' => 0,
                'vraag2 - optie 3' => 1,
            ],
        ];
        yield $multipleVariableMultipleRecordsMultipleChoice;

        yield [
            new VariableSet(
                new MultipleChoiceVariable(
                    name: 'question1',
                    dataPath: ['question1'],
                    options: [
                        new StringValueOption('option1', [
                            'default' => 'option 1',
                            'nl' => 'optie 1',
                        ]),
                        new RefuseValueOption([
                            'default' => 'Refused',
                            'nl' => 'Geweigerd'
                        ])
                    ],
                    titles: [
                        'default' => 'question1',
                        'nl' => 'vraag1',
                    ]
                )
            ),
            [
                [
                    'question1' => 'refused'
                ]
            ],
            'default',
            [
                [
                    'question1 - option 1' => 'refused'
                ]
            ]
        ];
    }
}
