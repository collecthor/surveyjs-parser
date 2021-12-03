<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Values;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Tests\support\TestValueOptionLabels;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\Values\IntegerValueOption
 */
class IntegerValueOptionTest extends TestCase
{
    use TestValueOptionLabels;

    /**
     * @param array<string, string> $labels
     * @return ValueOptionInterface
     */
    private function createOption(array $labels): ValueOptionInterface
    {
        return new IntegerValueOption(14, $labels);
    }

    /**
     * @return iterable<non-empty-list<array<string,string>>>
     */
    public function labelProvider(): iterable
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
        $this->assertValueOptionLabels($this->createOption(...), $labels);
    }
}
