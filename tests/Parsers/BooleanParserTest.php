<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\BooleanParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

#[CoversClass(BooleanParser::class)]
final class BooleanParserTest extends TestCase
{
    public function testParseBooleanQuestion(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            'type' => 'boolean',
            'name' => 'question1',
        ];

        $parser = new BooleanParser(
            [
                'default' => 'true',
            ],
            [
                'default' => 'false',
            ]
        );

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));
        self::assertInstanceOf(BooleanVariable::class, $result[0]);
    }
}
