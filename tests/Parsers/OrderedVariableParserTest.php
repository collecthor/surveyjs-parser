<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Interfaces\MultipleChoiceVariableInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\OrderedVariableParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(OrderedVariableParser::class)]
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

        self::assertInstanceOf(MultipleChoiceVariableInterface::class, $result[0]);
        self::assertTrue($result[0]->isOrdered());
        $options = $result[0]->getOptions();

        self::assertSame('item1', $options[0]->getDisplayValue());
        self::assertSame('item2', $options[1]->getDisplayValue());
        self::assertSame('item3', $options[2]->getDisplayValue());
    }
}
