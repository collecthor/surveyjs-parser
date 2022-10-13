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
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 */
final class PanelParserTest extends TestCase
{
    public function testRecursionOnElements(): void
    {
        $elements = [
            ['a' => 'b'],
            ['c' => 'd']
        ];

        $config = new SurveyConfiguration();
        $parent = $this->getMockBuilder(ElementParserInterface::class)->getMock();

        $parent->expects(self::exactly(2))
            ->method('parse')->withConsecutive(
                [self::equalTo($parent), self::equalTo($elements[0]), self::equalTo($config)],
                [self::equalTo($parent), self::equalTo($elements[1]), self::equalTo($config)]
            );
        $parser = new PanelParser();

        toArray($parser->parse($parent, ['elements' => $elements], $config));
    }

    public function testInvalidElements(): void
    {
        $parser = new PanelParser();
        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parser, ['elements' => 'not iterable'], new SurveyConfiguration()));
    }
}
