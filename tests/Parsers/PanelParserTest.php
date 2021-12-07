<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\PanelParser
 */
class PanelParserTest extends TestCase
{
    public function testRecursionOnElements(): void
    {
        $elements = [
            ['a' => 'b'],
            ['c' => 'd']
        ];

        $config = new SurveyConfiguration();
        $parent = $this->getMockBuilder(ElementParserInterface::class)->getMock();

        $parent->expects($this->exactly(2))
            ->method('parse')->withConsecutive(
                [$this->equalTo($parent), $this->equalTo($elements[0]), $this->equalTo($config)],
                [$this->equalTo($parent), $this->equalTo($elements[1]), $this->equalTo($config)]
            );
        $parser = new PanelParser();

        toArray($parser->parse($parent, ['elements' => $elements], $config));
    }

    public function testMissingElements(): void
    {
        $parser = new PanelParser();
        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parser, ['a' => 'b'], new SurveyConfiguration()));
    }

    public function testInvalidElements(): void
    {
        $parser = new PanelParser();
        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parser, ['elements' => 'not iterable'], new SurveyConfiguration()));
    }
}
