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
}
