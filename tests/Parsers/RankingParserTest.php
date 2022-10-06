<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\RankingParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\RankingParser
 * @uses \Collecthor\SurveyjsParser\Variables\BooleanVariable
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 */
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

        self::assertCount(3, $result);

        foreach ($result as $variable) {
            self::assertInstanceOf(SingleChoiceVariable::class, $variable);
        }
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

        self::assertSame("question5 (0)", $result[0]->getTitle());
        self::assertSame("question5 (1)", $result[1]->getTitle());
        self::assertSame("question5 (2)", $result[2]->getTitle());
    }
}
