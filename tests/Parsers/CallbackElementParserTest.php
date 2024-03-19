<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

#[CoversClass(CallbackElementParser::class)]
final class CallbackElementParserTest extends TestCase
{
    public function testCallbackIsCalled(): void
    {
        $parent = new DummyParser();
        $config = ['a' => 'b'];
        $surveyConfiguration = new SurveyConfiguration();

        $count = 0;

        $parser = new CallbackElementParser(function (ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfig) use ($parent, $config, $surveyConfiguration, &$count) {
            Assert::assertSame($parent, $root);
            Assert::assertSame($config, $questionConfig);
            Assert::assertSame($surveyConfiguration, $surveyConfig);
            $count++;
            return [];
        });

        self::assertSame(0, $count);
        toArray($parser->parse($parent, $config, $surveyConfiguration));
        /** @phpstan-ignore-next-line */
        self::assertSame(1, $count);
    }
}
