<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Helpers;

use Collecthor\SurveyjsParser\Helpers\ExpressionParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExpressionParser::class)]
final class ExpressionParserTest extends TestCase
{
    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function validExpressions(): array
    {
        return [
            ["5 + 123", "Addition(Value(5), Value(123))"],
            ["5.14", "Value(5.14)"],
            ["5", "Value(5)"],
            ['"test"', 'Value("test")'],
            ["'test'", 'Value("test")'],
            ["{abc.def}", 'Variable(abc.def)'],
            ["func(5)", "func(Value(5))"],
            ["5 + 123 * 5", "Addition(Value(5), Multiplication(Value(123), Value(5)))"],

            ["4 * 2 + 5 + 123 / 5", "Addition(Multiplication(Value(4), Value(2)), Addition(Value(5), Division(Value(123), Value(5))))"],
            ["randomSubset('V001',1)", 'randomSubset(Value("V001"), Value(1))'],
            ["{r1[0]}", 'Variable(r1, 0)'],
            ["randomSubset('V001', 2 + 4)", 'randomSubset(Value("V001"), Addition(Value(2), Value(4)))'],
            ["{S001}=4 or {S002}=3", 'Or(Eq(Variable(S001), Value(4)), Eq(Variable(S002), Value(3)))'],
            ["iif(({ppc} anyof [7065,7075]), 1, 0)", "iif(AnyOf(Variable(ppc), Value(7065), Value(7075)), Value(1), Value(0))"]

        ];
    }

    #[DataProvider('validExpressions')]
    public function testSuccess(string $expression, string $expected): void
    {
        $parser = new ExpressionParser();
        $node = $parser->parse($expression);
        self::assertSame($expected, $node->print());
    }
}
