<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\NoUISliderParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\FloatVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(NoUISliderParser::class)]
final class NoUISliderParserTest extends TestCase
{
    public function testParseDefault(): void
    {
        $surveyConfig = new SurveyConfiguration();

        $questionConfig = [
            "type" => "nouislider",
            "name" => "question1",
        ];

        $parser = new NoUISliderParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];

        self::assertInstanceOf(FloatVariable::class, $result);
        self::assertSame("question1", $result->getTitle());
    }
}
