<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Tests\support\NameTests;
use Collecthor\SurveyjsParser\Tests\support\ValueNameTests;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use Exception;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Values\IntegerValueOption
 * @uses \Collecthor\SurveyjsParser\Traits\GetDisplayValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\ResolvableVariableSet
 * @uses \Collecthor\SurveyjsParser\Variables\DeferredVariable
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
        toArray($parser->parse(new DummyParser(), [
            'name' => 'test',
        ], new SurveyConfiguration()));
    }

    public function testChoicesFromQuestionSimple(): void
    {
        $questionConfig =
            [
                'name' => 'question1',
                'choicesFromQuestion' => 'question2',
            ];
        $parser = $this->getParser();
        $question1 = toArray($parser->parse(new DummyParser(), $questionConfig, new SurveyConfiguration()))[0];

        self::assertInstanceOf(DeferredVariable::class, $question1);

        /** @var DeferredVariable $question1 */

        $valueOptions = [
            new StringValueOption('a', ['default' => 'a']),
            new StringValueOption('b', ['default' => 'b']),
            new StringValueOption('c', ['default' => 'c']),
        ];

        $question2 = new SingleChoiceVariable('question2', ['default' => 'question2'], $valueOptions, ['question2']);

        $resolvable = new ResolvableVariableSet($question1, $question2);

        $resolved = $question1->resolve($resolvable);

        self::assertInstanceOf(SingleChoiceVariable::class, $resolved);
        /** @var SingleChoiceVariable $resolved */
        self::assertSame($valueOptions, $resolved->getValueOptions());
    }

    public function testDontStoreOthersAsComment(): void
    {
        $questionConfig = [
            'name' => 'test',
            'choices' => [
                'a',
                'b',
                'c',
            ],
            'hasOther' => true,
        ];

        $surveyConfig = new SurveyConfiguration(storeOthersAsComment: false);
        $parser = $this->getParser();

        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];

        self::assertInstanceOf(OpenTextVariable::class, $result);
        self::assertEquals('test', $result->getName());
    }

    public function testDontAllowChoicesFromQuestionAndOthersAsComment(): void
    {
        $questionConfig = [
            'name' => 'test',
            'choicesFromQuestion' => 'question2',
            'hasOther' => true,
        ];

        $surveyConfig = new SurveyConfiguration(storeOthersAsComment: false);
        $parser = $this->getParser();
        self::expectException(Exception::class);
        $result = toArray($parser->parse(new DummyParser(), $questionConfig, $surveyConfig))[0];
    }

    protected function getParser(): ElementParserInterface
    {
        return new SingleChoiceQuestionParser();
    }
}
