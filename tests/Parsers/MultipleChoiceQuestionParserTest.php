<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Tests\support\RawConfigurationTests;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Exception;
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
    use RawConfigurationTests;
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

    public function testStoreDontOthersAsComment(): void
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
        return new MultipleChoiceQuestionParser();
    }

    /**
     * @return non-empty-list<non-empty-array<string, mixed>>
     */
    protected function validConfigs(): array
    {
        return [
            [
                'choices' => [
                    ['value' => 'a', 'text' => 'b'],
                    'c',
                    15,
                    ['value' => 16, 'text' => 'abc']
                ],
                'name' => 'q1',
            ],
            [
                'choices' => [
                    ['value' => 'a', 'text' => 'b'],
                    'c',
                    15,
                    ['value' => 16, 'text' => 'abc']
                ],
                'name' => 'q1',
                'hasComment' => true,
                'hasOther' => true,
            ]
        ];
    }
}
