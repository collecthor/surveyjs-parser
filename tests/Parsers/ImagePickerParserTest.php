<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\ImagePickerParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\ImagePickerParser
 * @uses \Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 */
final class ImagePickerParserTest extends TestCase
{
    public function testParseSingleImagePicker(): void
    {
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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

        $options = $result[0]->getValueOptions();
        self::assertSame('lion', $options[0]->getRawValue());
        self::assertSame('giraffe', $options[1]->getRawValue());
        self::assertSame('panda', $options[2]->getRawValue());
        self::assertSame('camel', $options[3]->getRawValue());
    }

    public function testDisplayValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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

        $options = $result[0]->getValueOptions();
        self::assertSame('lion', $options[0]->getDisplayValue());
        self::assertSame('giraffe', $options[1]->getDisplayValue());
        self::assertSame('panda', $options[2]->getDisplayValue());
        self::assertSame('camel', $options[3]->getDisplayValue());
    }

    public function testTextValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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

        $options = $result[0]->getValueOptions();
        self::assertSame('Lion', $options[0]->getDisplayValue());
        self::assertSame('Giraffe', $options[1]->getDisplayValue());
        self::assertSame('Panda', $options[2]->getDisplayValue());
        self::assertSame('Camel', $options[3]->getDisplayValue());
    }

    public function testLocalizedDisplayValueOptions(): void
    {
        $surveyConfig = new SurveyConfiguration(locales:['default', 'nl']);
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

        $options = $result[0]->getValueOptions();
        self::assertSame('Lion', $options[0]->getDisplayValue());
        self::assertSame('Giraffe', $options[1]->getDisplayValue());
        self::assertSame('Panda', $options[2]->getDisplayValue());
        self::assertSame('Camel', $options[3]->getDisplayValue());

        self::assertSame('Leeuw', $options[0]->getDisplayValue('nl'));
        self::assertSame('Giraf', $options[1]->getDisplayValue('nl'));
        self::assertSame('Panda', $options[2]->getDisplayValue('nl'));
        self::assertSame('Kameel', $options[3]->getDisplayValue('nl'));
    }
}
