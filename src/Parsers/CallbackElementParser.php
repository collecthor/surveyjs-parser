<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser\Parsers;

use Closure;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;

/**
 * An element parser that uses a closure to parse any given question configuration.
 * The closure should have the same signature as `ElementParserInterface::parse`
 * @see ElementParserInterface::parse()
 */
final readonly class CallbackElementParser implements ElementParserInterface
{
    /**
     * @param Closure(ElementParserInterface, array<string|int, mixed>, SurveyConfiguration, list<string>): iterable<VariableInterface | DeferredVariable> $callback
     */
    public function __construct(private Closure $callback)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        yield from ($this->callback)($root, $questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
