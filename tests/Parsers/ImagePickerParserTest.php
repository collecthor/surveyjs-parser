<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\ImagePickerParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(ImagePickerParser::class)]
final class ImagePickerParserTest extends TestCase
{
    public function testParseSingleImagePicker(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "choices" => [
                [
                    "value" => "lion",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ]
            ]
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(SingleChoiceVariable::class, $result[0]);
    }

    public function testParseMultipleImagePicker(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "choices" => [
                [
                    "value" => "lion",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ],
            ],
            "multiSelect" => true,
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(MultipleChoiceVariable::class, $result[0]);
    }

    public function testValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "choices" => [
                [
                    "value" => "lion",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ]
            ]
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));
        self::assertCount(1, $result);
        $question = $result[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $question);
        $options = $question->getOptions();
        self::assertCount(4, $options);
        self::assertSame('lion', $options[0]->getValue());
        self::assertSame('giraffe', $options[1]->getValue());
        self::assertSame('panda', $options[2]->getValue());
        self::assertSame('camel', $options[3]->getValue());
    }

    public function testDisplayValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "choices" => [
                [
                    "value" => "lion",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ]
            ]
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));
        self::assertCount(1, $result);
        $question = $result[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $question);
        $options = $question->getOptions();
        self::assertCount(4, $options);
        self::assertSame('lion', $options[0]->getDisplayValue());
        self::assertSame('giraffe', $options[1]->getDisplayValue());
        self::assertSame('panda', $options[2]->getDisplayValue());
        self::assertSame('camel', $options[3]->getDisplayValue());
    }

    public function testTextValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "choices" => [
                [
                    "value" => "lion",
                    "text" => "Lion",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "text" => "Giraffe",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "text" => "Panda",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "text" => "Camel",
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ]
            ]
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));
        self::assertCount(1, $result);
        $question = $result[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $question);
        $options = $question->getOptions();
        self::assertCount(4, $options);
        self::assertSame('Lion', $options[0]->getDisplayValue());
        self::assertSame('Giraffe', $options[1]->getDisplayValue());
        self::assertSame('Panda', $options[2]->getDisplayValue());
        self::assertSame('Camel', $options[3]->getDisplayValue());
    }

    public function testLocalizedDisplayValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "choices" => [
                [
                    "value" => "lion",
                    "text" => [
                        "default" => "Lion",
                        "nl" => "Leeuw",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "text" => [
                        "default" => "Giraffe",
                        "nl" => "Giraf",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "text" => [
                        "default" => "Panda",
                        "nl" => "Panda",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "text" => [
                        "default" => "Camel",
                        "nl" => "Kameel",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ]
            ]
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(1, $result);
        $question = $result[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $question);
        $options = $question->getOptions();
        self::assertCount(4, $options);
        self::assertSame('Lion', $options[0]->getDisplayValue());
        self::assertSame('Giraffe', $options[1]->getDisplayValue());
        self::assertSame('Panda', $options[2]->getDisplayValue());
        self::assertSame('Camel', $options[3]->getDisplayValue());

        self::assertSame('Leeuw', $options[0]->getDisplayValue('nl'));
        self::assertSame('Giraf', $options[1]->getDisplayValue('nl'));
        self::assertSame('Panda', $options[2]->getDisplayValue('nl'));
        self::assertSame('Kameel', $options[3]->getDisplayValue('nl'));
    }

    public function testParseCommentField(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "imagepicker",
            "name" => "question3",
            "showCommentArea" => true,
            "commentText" => "Comment text",
            "choices" => [
                [
                    "value" => "lion",
                    "text" => [
                        "default" => "Lion",
                        "nl" => "Leeuw",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/lion.jpg"
                ],
                [
                    "value" => "giraffe",
                    "text" => [
                        "default" => "Giraffe",
                        "nl" => "Giraf",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/giraffe.jpg"
                ],
                [
                    "value" => "panda",
                    "text" => [
                        "default" => "Panda",
                        "nl" => "Panda",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/panda.jpg"
                ],
                [
                    "value" => "camel",
                    "text" => [
                        "default" => "Camel",
                        "nl" => "Kameel",
                    ],
                    "imageLink" => "https://surveyjs.io/Content/Images/examples/image-picker/camel.jpg"
                ]
            ]
        ];

        $parser = new ImagePickerParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(2, $result);
        [$q1, $q2] = $result;
        self::assertInstanceOf(SingleChoiceVariable::class, $q1);
        self::assertInstanceOf(OpenTextVariable::class, $q2);
        self::assertSame('question3 - Comment text', $q2->getTitle());
    }
}
