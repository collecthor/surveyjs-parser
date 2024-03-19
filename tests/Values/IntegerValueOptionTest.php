<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Tests\support\TestValueOptionLabels;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IntegerValueOption::class)]
final class IntegerValueOptionTest extends TestCase
{
    use TestValueOptionLabels;

    /**
     * @param array<string, string> $labels
     */
    private function createOption(array $labels = []): IntegerValueOption
    {
        return new IntegerValueOption(14, $labels);
    }

    /**
     * @return iterable<non-empty-list<array<string,string>>>
     */
    public static function labelProvider(): iterable
    {
        yield [[]];
        yield [[
            "locale1" => "test",
            "locale2" => "test2"
        ]];
        yield [[
            ValueOptionInterface::DEFAULT_LOCALE => 'test3'
        ]];
    }

    /**
     * @param array<string,string> $labels
     */
    #[DataProvider('labelProvider')]
    public function testLabelSet(array $labels): void
    {
        self::assertValueOptionLabels($this->createOption(...), $labels);
    }
}
