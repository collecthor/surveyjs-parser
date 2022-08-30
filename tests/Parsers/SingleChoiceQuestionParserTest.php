<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Tests\support\NameTests;
use Collecthor\SurveyjsParser\Tests\support\ValueNameTests;
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
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 */
final class SingleChoiceQuestionParserTest extends TestCase
{
    use ValueNameTests;
    use NameTests;
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

    public function testHasNone(): void
    {
        $parent = new DummyParser();
        $surveyConfiguration = new SurveyConfiguration();


        $parser = new SingleChoiceQuestionParser();
        $variable = toArray($parser->parse($parent, [
            'name' => 'test',
            'choices' => [
                'a'
            ],
            'hasNone' => true,
            'hasOther' => true,
        ], $surveyConfiguration))[0];

        self::assertInstanceOf(SingleChoiceVariable::class, $variable);
        $options = $variable->getValueOptions();
        self::assertCount(3, $options);
        $rawValues = [$options[0]->getRawValue(), $options[1]->getRawValue(), $options[2]->getRawValue()];
        self::assertContains('a', $rawValues);
        self::assertContains('other', $rawValues);
        self::assertContains('none', $rawValues);
    }

    public function testChoicesWrongType(): void
    {
        $parser = new SingleChoiceQuestionParser();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected to find an array at key choices");
        toArray($parser->parse(new DummyParser(), [
            'name' => 'test',
            'choices' => 15
        ], new SurveyConfiguration()));
    }

    public function testMissingChoices(): void
    {
        $parser = new SingleChoiceQuestionParser();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Choices must be a non empty list");
        toArray($parser->parse(new DummyParser(), [
            'name' => 'test',
        ], new SurveyConfiguration()));
    }
    protected function getParser(): ElementParserInterface
    {
        return new SingleChoiceQuestionParser();
    }
}
