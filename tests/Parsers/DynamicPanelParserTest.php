<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Parsers\DynamicPanelParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\SurveyParser;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\DynamicPanelParser
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Parsers\TextQuestionParser
 * @uses \Collecthor\SurveyjsParser\Traits\GetTitle
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 */
final class DynamicPanelParserTest extends TestCase
{
    public function testInvokeRootParser(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "paneldynamic",
            "name" => "question1",
            "templateElements" => [
                [
                    "type" => "text",
                    "name" => "question2"
                ],
                [
                    "type" => "comment",
                    "name" => "question3"
                ]
                ],
            "maxPanelCount" => 1,
        ];
        $rootParser = $this->createMock(ElementParserInterface::class);
        $rootParser->expects(self::exactly(2))->method('parse');
        $parser = new DynamicPanelParser(['default' => 'row']);

        $result = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testElementNames(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "paneldynamic",
            "name" => "question1",
            "templateElements" => [
                [
                    "type" => "text",
                    "name" => "question2"
                ],
                [
                    "type" => "text",
                    "name" => "question3"
                ]
                ],
            "maxPanelCount" => 2,
        ];
        $rootParser = $this->createMock(ElementParserInterface::class);
        $rootParser->expects(self::exactly(4))->method('parse')->withAnyParameters()->willReturnCallback(function ($p, array $questionConfig) use ($rootParser) {
            self::assertSame($rootParser, $p);
            self::assertSame('text', $questionConfig['type']);
            return [];
        });

        $parser = new DynamicPanelParser(['default' => 'row']);

        $result = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
    }

    public function testRootParserResult(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "paneldynamic",
            "name" => "question1",
            "templateElements" => [
                [
                    "type" => "text",
                    "name" => "question2"
                ],
                [
                    "type" => "comment",
                    "name" => "question3"
                ]
                ],
            "maxPanelCount" => 2,
        ];
        $rootParser = new TextQuestionParser();
        $parser = new DynamicPanelParser(['default' => 'row']);

        $result = toArray($parser->parse($rootParser, $questionConfig, $surveyConfig));
        self::assertSame("question1 row 0 question2", $result[0]->getTitle());
    }
}
