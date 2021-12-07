<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\CallbackElementParser
 */
class CallbackElementParserTest extends TestCase
{
    public function testCallbackIsCalled(): void
    {
        $parent = new DummyParser();
        $config = ['a' => 'b'];
        $surveyConfiguration = new SurveyConfiguration();

        $mock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $mock->expects($this->once())
            ->method('__invoke')
            ->with($this->equalTo($parent), $this->equalTo($config))
            ->willReturn([]);

        /** @var callable $mock */
        $this->assertIsCallable($mock);
        $closure = \Closure::fromCallable($mock);
        $parser = new CallbackElementParser($closure);

        toArray($parser->parse($parent, $config, $surveyConfiguration));
    }
}
