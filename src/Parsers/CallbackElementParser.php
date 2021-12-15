<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

/**
 * An element parser that uses a closure to parse any given question configuration.
 * The closure should have the same signature as `ElementParserInterface::parse`
 * @see ElementParserInterface::parse()
 */
class CallbackElementParser implements ElementParserInterface
{
    public function __construct(private \Closure $callback)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        yield from ($this->callback)($root, $questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
