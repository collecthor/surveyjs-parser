<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\OrderedVariableParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OrderedVariable;
use PHPUnit\Framework\TestCase;

use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\OrderedVariableParser
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Variables\OrderedVariable
 */
final class OrderedVariableParserTest extends TestCase
{
    public function testParseRanking(): void
    {
        $surveyConfig = new SurveyConfiguration();
        $questionConfig = [
            'type' => 'ranking',
            'name' => 'question1',
            'choices' => [
                'item1',
                'item2',
                'item3'
            ],
        ];

        $parser = new OrderedVariableParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig));

        self::assertInstanceOf(OrderedVariable::class, $result[0]);
        $options = $result[0]->getValueOptions();

        self::assertSame('item1', $options[0]->getDisplayValue());
        self::assertSame('item2', $options[1]->getDisplayValue());
        self::assertSame('item3', $options[2]->getDisplayValue());
    }
}
