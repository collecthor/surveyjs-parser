<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\SurveyParser;
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
        $element = ['a' => 'b'];

        $config = new SurveyConfiguration();
        $parent = $this->getMockBuilder(ElementParserInterface::class)->getMock();

        $parent->expects(self::exactly(2))
            ->method('parse')->with($parent, $element, $config);

        $parser = new PanelParser();

        toArray($parser->parse($parent, ['elements' => [$element, $element]], $config));
    }

    public function testInvalidElements(): void
    {
        $parser = new PanelParser();
        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse($parser, ['elements' => 'not iterable'], new SurveyConfiguration()));
    }
}
