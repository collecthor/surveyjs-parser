<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 */
class SingleChoiceQuestionParserTest extends TestCase
{
    /**
     * @return iterable<array<mixed>>
     */
    public function badChoicesProvider(): iterable
    {
        yield [['value' => 'abc']];
        yield [['text' => 'abc']];
        yield [['value' => 'abc', 'text' => 15]];
        yield [['value' => ['abc'], 'text' => 'ab4']];
    }

    /**
     * @dataProvider badChoicesProvider
     * @param array<mixed> $choice
     */
    public function testBadChoices(array $choice): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();
        $parser = new SingleChoiceQuestionParser();

        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parent, [
            'choices' => [
                $choice
            ],
            'name' => 'q1',

        ], $surveyConfiguration));
    }


    public function testKeyValueChoices(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();
        $parser = new SingleChoiceQuestionParser();

        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parent, [
            'choices' => [
                'a' => 'b',
                'c' => 'd'
            ],
            'name' => 'q1',

        ], $surveyConfiguration));
    }


    public function testChoices(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();


        $parser = new SingleChoiceQuestionParser();

        $variable = toArray($parser->parse($parent, [
            'choices' => [
                ['value' => 'a', 'text' => 'b'],
                'c',
                15,
                ['value' => 16, 'text' => 'abc']
            ],
            'name' => 'q1',

        ], $surveyConfiguration))[0];
        self::assertInstanceOf(SingleChoiceVariable::class, $variable);

        $options = $variable->getValueOptions();
        self::assertCount(4, $options);
        self::assertSame('a', $options[0]->getRawValue());
        self::assertSame('c', $options[1]->getRawValue());
        self::assertSame(15, $options[2]->getRawValue());
        self::assertSame(16, $options[3]->getRawValue());

        self::assertSame('b', $options[0]->getDisplayValue());
        self::assertSame('c', $options[1]->getDisplayValue());
        self::assertSame("15", $options[2]->getDisplayValue());
        self::assertSame('abc', $options[3]->getDisplayValue());
    }
}
