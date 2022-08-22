<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @mixin TestCase
 */
trait NameTests
{
    abstract protected function getParser(): ElementParserInterface;

    final public function testMissingName(): void
    {
        $parser = $this->getParser();
        $this->expectException(\InvalidArgumentException::class);
        toArray($parser->parse(new DummyParser(), [
            'valueName' => "test"
        ], new SurveyConfiguration()));
    }
}
