<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ArrayRecord;
use Collecthor\SurveyjsParser\Parsers\CommentParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(CommentParser::class)]
final class CommentParserTest extends TestCase
{
    public function testEmpty(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(['abc' => 'def'], new SurveyConfiguration());
        self::assertEmpty(toArray($variables));
    }

    public function testHasOther(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(['hasOther' => true, 'name' => 'q1'], new SurveyConfiguration());
        self::assertCount(1, toArray($variables));
    }

    public function testHasComment(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(['hasComment' => true, 'name' => 'q1'], new SurveyConfiguration());
        self::assertCount(1, toArray($variables));
    }

    public function testHasCommentAndOther(): void
    {
        $parser = new CommentParser();
        $variables = $parser->parse(['hasComment' => true, 'hasOther' => true, 'name' => 'q1'], new SurveyConfiguration());
        self::assertCount(1, toArray($variables));
    }

    public function testCustomPostfix(): void
    {
        $surveyConfiguration = new SurveyConfiguration('-' . random_bytes(15));

        $parser = new CommentParser();
        $variable = toArray($parser->parse(['hasComment' => true, 'name' => 'q1'], $surveyConfiguration));
        self::assertCount(1, $variable);

        $record = new ArrayRecord([
            "q1{$surveyConfiguration->commentSuffix}" => 'abcdef'
        ], 1, new \DateTime(), new \DateTime());

        $value = $variable[0]->getValue($record);
        self::assertInstanceOf(StringValue::class, $value);

        self::assertSame('abcdef', $value->getRawValue());
    }
}
