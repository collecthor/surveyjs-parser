<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MultipleTextParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(MultipleTextParser::class)]
final class MultipleTextParserTest extends TestCase
{
    public function testGetRightAmountOfVariables(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "multipletext",
            "name" => "question4",
            "items" => [
                [
                    "name" => "text1"
                ],
                [
                    "name" => "text2"
                ]
            ]
        ];

        $parser = new MultipleTextParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(2, $result);
    }

    public function testGetTranslations(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "multipletext",
            "name" => "question4",
            "title" => [
                "nl" => "question4",
                "default" => "question4",
            ],
            "items" => [
                [
                    "name" => "text1",
                    "title" => [
                        "nl" => "tekst1",
                        "default" => "text1",
                    ],
                ],
                [
                    "name" => "text2",
                    "title" => [
                        "nl" => "tekst2",
                        "default" => "text2",
                    ],
                ],
            ],
        ];
        $parser = new MultipleTextParser();
        /** @var list<OpenTextVariable> $result */
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertSame('question4 - text1', $result[0]->getTitle());
        self::assertSame('question4 - tekst1', $result[0]->getTitle('nl'));
    }

    public function testGetRightVariableType(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "multipletext",
            "name" => "question4",
            "items" => [
                [
                    "name" => "text1",
                    "title" => [
                        "nl" => "tekst1",
                        "default" => "text1",
                    ],
                ],
                [
                    "name" => "text2",
                    "title" => [
                        "nl" => "tekst2",
                        "default" => "text2",
                    ],
                ],
            ],
        ];
        $parser = new MultipleTextParser();
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        foreach ($result as $variable) {
            self::assertInstanceOf(OpenTextVariable::class, $variable);
        }
    }
}
