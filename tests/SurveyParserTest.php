<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\SurveyParser;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\VariableSet;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\SurveyParser
 * @uses \Collecthor\SurveyjsParser\Parsers\CallbackElementParser
 * @uses \Collecthor\SurveyjsParser\VariableSet
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\ParserHelpers
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Parsers\CommentParser
 * @uses \Collecthor\SurveyjsParser\Parsers\TextQuestionParser
 * @uses \Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Parsers\ChainedParser
 */
class SurveyParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-list<mixed>>
     */
    public function sampleProvider(): iterable
    {
        $files = glob(__DIR__ . '/samples/*.json');
        foreach (is_array($files) ? $files : [] as $fileName) {
            $contents = file_get_contents($fileName);
            if (is_string($contents)) {
                yield [json_decode($contents, true)];
            }
        }
    }
    /**
     * @coversNothing
     * @dataProvider sampleProvider
     * @param array<string, mixed> $surveyConfig
     */
    public function testSamples(array $surveyConfig): void
    {
        $parser = new SurveyParser();
        $variableSet = $parser->parseSurveyStructure($surveyConfig);
        self::assertInstanceOf(VariableSet::class, $variableSet);
    }

    public function testParseEmptySurvey(): void
    {
        $parser = new SurveyParser();
        $set = $parser->parseSurveyStructure([
            'commentPrefix' => 'thisisapostfix',
        ]);
        self::assertCount(0, toArray($set->getVariables()));
    }

    public function testParsePageWithElement(): void
    {
        $parser = new SurveyParser();
        $set = $parser->parseSurveyStructure([
            'commentPrefix' => 'thisisapostfix',
            'pages' => [
                [
                    'name' => 'test',
                    'elements' => [
                        [
                            'type' => 'text',
                            'title' => 'question text',
                            'name' => 'question1',
                            'hasComment' => true
                        ]
                    ]
                ]
            ]
        ]);

        self::assertCount(2, toArray($set->getVariables()));
        $variable = $set->getVariable('question1');

        self::assertSame('question text', $variable->getTitle());

        self::assertInstanceOf(OpenTextVariable::class, $set->getVariable('question1.comment'));
    }
}
