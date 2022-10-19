<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\DataInterfaces\ValueInterface;
use Collecthor\SurveyjsParser\ArrayDataRecord;
use Collecthor\SurveyjsParser\ElementParserInterface;
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
 * @uses \Collecthor\SurveyjsParser\ParserLocalizer
 * @uses \Collecthor\SurveyjsParser\ResolvableVariableSet
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Parsers\BooleanParser
 * @uses \Collecthor\SurveyjsParser\Parsers\CommentParser
 * @uses \Collecthor\SurveyjsParser\Parsers\DynamicPanelParser
 * @uses \Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser
 * @uses \Collecthor\SurveyjsParser\Parsers\TextQuestionParser
 * @uses \Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser
 * @uses \Collecthor\SurveyjsParser\Values\StringValueOption
 * @uses \Collecthor\SurveyjsParser\Variables\SingleChoiceVariable
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 */
class SurveyParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-list<mixed>>
     */
    public function sampleProvider(): iterable
    {
        $files = glob(__DIR__ . '/support/samples/*.json');
        foreach (is_array($files) ? $files : [] as $fileName) {
            $contents = file_get_contents($fileName);
            if (is_string($contents)) {
                yield [$contents];
            }
        }
    }
    /**
     * @dataProvider sampleProvider
     */
    public function testSamples(string $surveyJson): void
    {
        $parser = new SurveyParser();
        $variableSet = $parser->parseJson($surveyJson);
        self::assertInstanceOf(VariableSet::class, $variableSet);
        /** @var array{testConfig?:array{variableCount: int, samples?: list<array{data: array<string, mixed>, assertions: list<array{expected: mixed, variable: string}>}>}} $surveyConfig */
        $surveyConfig = json_decode($surveyJson, true);
        if (isset($surveyConfig['testConfig'])) {
            $testConfig = $surveyConfig['testConfig'];
            self::assertCount($testConfig['variableCount'], [...$variableSet->getVariables()]);
            if (isset($testConfig['samples'])) {
                foreach ($testConfig['samples'] as $sample) {
                    $data = new ArrayDataRecord($sample['data']);
                    foreach ($sample['assertions'] as $assertion) {
                        $value = $variableSet->getVariable($assertion['variable'])->getValue($data);
                        if ($value instanceof ValueInterface) {
                            self::assertSame(
                                $assertion['expected'],
                                $value->getRawValue()
                            );
                        } else {
                            throw new \Exception('Value sets not supported yet by this test');
                        }
                    }
                }
            }
        }
    }

    public function testInvalidJsonData(): void
    {
        $parser = new SurveyParser();
        self::expectException(\InvalidArgumentException::class);
        $parser->parseJson('[1 ,5]');
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

    public function testAllParsersAreCalled(): void
    {
        $parser = new SurveyParser();

        $once = $this->getMockBuilder(ElementParserInterface::class)->getMock();
        $twice = $this->getMockBuilder(ElementParserInterface::class)->getMock();

        $once->expects(self::once())->method('parse');
        $twice->expects(self::exactly(2))->method('parse');
        $parser->addParser('test', $twice);
        $parser->addParser('test', $once);
        $parser->addParser('test', $twice);

        $parser->parseSurveyStructure([
            'pages' => [
                [
                    'elements' => [
                        [
                            'type' => 'test',
                        ]
                    ]
                ]
            ]
        ]);
    }
}
