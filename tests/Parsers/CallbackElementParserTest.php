<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @covers \Collecthor\SurveyjsParser\Parsers\CallbackElementParser
 * @uses \Collecthor\SurveyjsParser\SurveyConfiguration
 */
final class CallbackElementParserTest extends TestCase
{
    public function testCallbackIsCalled(): void
    {
        $parent = new DummyParser();
        $config = ['a' => 'b'];
        $surveyConfiguration = new SurveyConfiguration();

        $mock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        /** @phpstan-ignore-next-line */
        $mock->expects($this->once())
            ->method('__invoke')
            ->with(self::equalTo($parent), self::equalTo($config))
            ->willReturn([]);
        self::assertIsCallable($mock);
        $closure = \Closure::fromCallable($mock);
        $parser = new CallbackElementParser($closure);

        toArray($parser->parse($parent, $config, $surveyConfiguration));
    }
}
