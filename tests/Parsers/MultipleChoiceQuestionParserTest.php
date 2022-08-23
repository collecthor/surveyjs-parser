<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 */

final class MultipleChoiceQuestionParserTest extends TestCase
{
    public function testMultipleChoice(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();

        $parser = new MultipleChoiceQuestionParser();

        $questionConfig = [
            'choices' => [
                ['value' => 'a', 'text' => 'b'],
                'c',
                15,
                ['value' => 16, 'text' => 'abc']
            ],
            'name' => 'q1',
        ];

        
        $variable = toArray($parser->parse($parent, $questionConfig, $surveyConfiguration))[0];
        self::assertInstanceOf(MultipleChoiceVariable::class, $variable);
        self::assertCount(4, $variable->getValueOptions());

        $options = $variable->getValueOptions();

        self::assertSame('b', $options[0]->getDisplayValue());
        self::assertSame('c', $options[1]->getDisplayValue());
        self::assertSame('15', $options[2]->getDisplayValue());
        self::assertSame('abc', $options[3]->getDisplayValue());
    }
}
