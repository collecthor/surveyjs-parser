<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Interfaces\MultipleChoiceVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\StringVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\RankingParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(RankingParser::class)]
final class RankingParserTest extends TestCase
{
    public function testNumVariables(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "ranking",
            "name" => "question5",
            "choices" => [
                "item1",
                "item2",
                "item3"
            ]
        ];

        $parser = new RankingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertCount(1, $result);
        self::assertInstanceOf(MultipleChoiceVariableInterface::class, $result[0]);
    }

    public function testVariableTitles(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            "type" => "ranking",
            "name" => "question5",
            "choices" => [
                "item1",
                "item2",
                "item3"
            ]
        ];

        $parser = new RankingParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));
        self::assertCount(1, $result);
        self::assertInstanceOf(VariableInterface::class, $result[0]);
        self::assertSame("question5", $result[0]->getTitle());
    }

    public function testExportsComment(): void
    {
        $json = <<<JSON
{
       "type": "ranking",
       "name": "q1",
       "choices": ["a", "b", "c"],
       "showCommentArea": true
      }
JSON;
        $parser = new RankingParser();

        $result = toArray($parser->parse(new DummyParser(), json_decode($json, true, JSON_THROW_ON_ERROR), new SurveyConfiguration()));

        self::assertCount(2, $result);
        self::assertInstanceOf(StringVariableInterface::class, $result[1]);
    }
}
