<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Tests\support\NameTests;
use Collecthor\SurveyjsParser\Tests\support\RawConfigurationTests;
use Collecthor\SurveyjsParser\Tests\support\ValueNameTests;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Variables\IntegerVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextQuestionParser::class)]
final class TextQuestionParserTest extends TestCase
{
    use RawConfigurationTests;
    use ValueNameTests;
    use NameTests;

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
            if (is_int($key) || is_string($key)) {
                $result[$key] = $value;
            } else {
                throw new \RuntimeException("Keys most be int or string for toArray");
            }
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

        $variable = $variables[0];
        self::assertInstanceOf(OpenTextVariable::class, $variable);


        $record = new ArrayRecord(['question1' => 'abc'], 1, new \DateTimeImmutable(), new \DateTimeImmutable());

        $value = $variable->getValue($record);
        self::assertInstanceOf(StringValue::class, $value);
        self::assertSame('abc', $value->getRawValue());
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

        $variable = $variables[0];
        self::assertInstanceOf(OpenTextVariable::class, $variable);


        $record = new ArrayRecord(['question2' => 'abc'], 1, new \DateTimeImmutable(), new \DateTimeImmutable());

        $value = $variable->getValue($record);
        self::assertInstanceOf(StringValue::class, $value);
        self::assertSame('abc', $value->getRawValue());
    }

    public function testNumberQuestion(): void
    {
        $dummy = new DummyParser();
        $config = new SurveyConfiguration();
        $parser = new TextQuestionParser();

        $variables = $this->toArray($parser->parse($dummy, [
            'name' => 'question1',
            'inputType' => 'number'

        ], $config, []));

        $variable = $variables[0];
        self::assertInstanceOf(IntegerVariable::class, $variable);
    }


    protected function getParser(): ElementParserInterface
    {
        return new TextQuestionParser();
    }

    /**
     * @return non-empty-list<non-empty-array<string, mixed>>
     */
    protected function validConfigs(): array
    {
        return [
            [
                'name' => 'test',
            ],
            [
                'name' => 'test',
                'hasComment' => true,
            ],
            [
                'name' => 'test',
                'inputType' => 'number'
            ]
        ];
    }
}
