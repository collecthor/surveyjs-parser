<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Parsers\CommentParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\CommentParser
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\ArrayRecord
 * @uses \Collecthor\SurveyjsParser\Values\StringValue
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 * @uses \Collecthor\SurveyjsParser\ArrayDataRecord
 */
class CommentParserTest extends TestCase
{
    public function testEmpty(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(new DummyParser(), ['abc' => 'def'], new SurveyConfiguration());
        self::assertEmpty(toArray($variables));
    }

    public function testHasOther(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(new DummyParser(), ['hasOther' => true, 'name' => 'q1'], new SurveyConfiguration());
        self::assertCount(1, toArray($variables));
    }

    public function testHasComment(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(new DummyParser(), ['hasComment' => true, 'name' => 'q1'], new SurveyConfiguration());
        self::assertCount(1, toArray($variables));
    }

    public function testHasCommentAndOther(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(new DummyParser(), ['hasComment' => true, 'hasOther' => true, 'name' => 'q1'], new SurveyConfiguration());
        self::assertCount(1, toArray($variables));
    }

    public function testCustomPostfix(): void
    {
        $surveyConfiguration = new SurveyConfiguration('-' . random_bytes(15));

        $parser = new CommentParser();
        $variable = toArray($parser->parse(new DummyParser(), ['hasComment' => true, 'name' => 'q1'], $surveyConfiguration))[0];
        self::assertInstanceOf(OpenTextVariable::class, $variable);

        $record = new ArrayRecord([
            "q1{$surveyConfiguration->commentPostfix}" => 'abcdef'
        ], 1, new \DateTime(), new \DateTime());

        $value = $variable->getValue($record);
        self::assertInstanceOf(StringValue::class, $value);

        self::assertSame('abcdef', $value->getRawValue());
    }
}
