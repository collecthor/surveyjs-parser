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
            ["iif(({ppc} anyof [7065,7075]), 1, 0)", "iif(AnyOf(Variable(ppc), Value(7065), Value(7075)), Value(1), Value(0))"],
            ["{Q008_AG} notempty + {Q002} contains 17 + {Q008_AB} notempty", 'Contains(Addition(NotEmpty(Variable(Q008_AG)), Variable(Q002)), Addition(Value(17), NotEmpty(Variable(Q008_AB))))'],
            ["if({random} empty,randInt(1,3),{random})", "if(Empty(Variable(random)), randInt(Value(1), Value(3)), Variable(random))"],
            ["randomSubset({T013Keuze},1)", "randomSubset(Variable(T013Keuze), Value(1))"],
            ["iif(({Folder1a} + {Folder2a} + {Folder3a} ) < 1, 1, 0)", "iif(Lt(Addition(Addition(Variable(Folder1a), Variable(Folder2a)), Variable(Folder3a)), Value(1)), Value(1), Value(0))"],
            ["iif({V006} notcontains '1', {Kiezer[1]}, iif({V006} contains '1', {Kiezer[0]}))", 'iif(NotContains(Variable(V006), Value("1")), Variable(Kiezer, 1), iif(Contains(Variable(V006), Value("1")), Variable(Kiezer, 0)))'],
            ['iif({S006} == 1, {S005a}, {S005a}/({S006}-{S007}))', 'iif(Eq2(Variable(S006), Value(1)), Variable(S005a), Division(Variable(S005a), Subtraction(Variable(S006), Variable(S007))))'],
            ["iif({Q11Q12_verborgen} contains 'Other namely', [{Q011-Comment}], {Q11Q12_verborgen})", 'iif(Contains(Variable(Q11Q12_verborgen), Value("Other namely")), Variable(Q011-Comment), Variable(Q11Q12_verborgen))'],
            ['randomSubset(T013Keuze,1)', 'randomSubset(UnescapedStringValue("T013Keuze"), Value(1))'],
            ['iif({channel} <> \'Belgie\', "sanitaire", "badkamer")', 'iif(NotEq(Variable(channel), Value("Belgie")), Value("sanitaire"), Value("badkamer"))'],
            ['({Getal0}+{Getal1}+{Getal2}+{Getal3}+{Getal4}) % 7', 'Modulus(Addition(Addition(Variable(Getal0), Variable(Getal1)), Addition(Addition(Variable(Getal2), Variable(Getal3)), Variable(Getal4))), Value(7))']
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
