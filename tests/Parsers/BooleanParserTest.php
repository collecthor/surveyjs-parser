<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\BooleanParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\BooleanParser
 * @uses \Collecthor\SurveyjsParser\Variables\BooleanVariable
 * @uses \Collecthor\SurveyjsParser\Values\BooleanValueOption
 */
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
