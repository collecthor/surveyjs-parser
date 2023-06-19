<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Tests\support\TestValueOptionLabels;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\StringValueOption
 */
class StringValueOptionTest extends TestCase
{
    use TestValueOptionLabels;

    /**
     * @param array<string, string> $labels
     */
    private function createOption(array $labels): StringValueOption
    {
        return new StringValueOption("rawtestvalue", $labels);
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
     * @dataProvider labelProvider
     * @param array<string,string> $labels
     * @return void
     */
    public function testLabelSet(array $labels): void
    {
        self::assertValueOptionLabels($this->createOption(...), $labels);
    }

    /**
     * @dataProvider labelProvider
     * @param array<string,string> $labels
     */
    public function testDisplayValues(array $labels): void
    {
        self::assertSame($labels, $this->createOption($labels)->getDisplayValues());
    }
}
