<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\TextQuestionParser
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable

 */
class TextQuestionParserTest extends TestCase
{
    /**
     * @template X
     * @template Y
     * @param iterable<X, Y> $iterable
     * @return array<X, Y>
     */
    private function toArray(iterable $iterable): array
    {
        $result = [];
        foreach ($iterable as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }
    public function testDataPathWithoutValueName(): void
    {
        $dummy = new DummyParser();
        $config = new SurveyConfiguration();
        $parser = new TextQuestionParser();
        $variables = $this->toArray($parser->parse($dummy, [
            'name' => 'question1',
        ], $config, []));

        /** @var OpenTextVariable $variable */
        $variable = $variables[0];
        $this->assertInstanceOf(OpenTextVariable::class, $variable);


        $record = new ArrayRecord(['question1' => 'abc'], 1, new \DateTimeImmutable(), new \DateTimeImmutable());

        $value = $variable->getValue($record);
        $this->assertInstanceOf(StringValue::class, $value);
        $this->assertSame('abc', $value->getRawValue());
    }


    public function testDataPathWithValueName(): void
    {
        $dummy = new DummyParser();
        $config = new SurveyConfiguration();
        $parser = new TextQuestionParser();
        $variables = $this->toArray($parser->parse($dummy, [
            'name' => 'question1',
            'valueName' => 'question2'
        ], $config, []));

        /** @var OpenTextVariable $variable */
        $variable = $variables[0];
        $this->assertInstanceOf(OpenTextVariable::class, $variable);


        $record = new ArrayRecord(['question2' => 'abc'], 1, new \DateTimeImmutable(), new \DateTimeImmutable());

        $value = $variable->getValue($record);
        $this->assertInstanceOf(StringValue::class, $value);
        $this->assertSame('abc', $value->getRawValue());
    }
}