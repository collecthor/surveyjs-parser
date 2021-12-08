<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\SurveyParser;
use Collecthor\SurveyjsParser\VariableSet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\SurveyParser
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
}
