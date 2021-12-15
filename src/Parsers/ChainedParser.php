<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

/**
 * This parser wraps 0 or more parsers and calls them in order.
 * Use this when a single question can have multiple variables.
 */
class ChainedParser implements ElementParserInterface
{
    /**
     * @var list<ElementParserInterface>
     */
    private array $parsers;

    public function __construct(ElementParserInterface ...$parsers)
    {
        $this->parsers = array_values($parsers);
    }

    public function parse(
        ElementParserInterface $root,
        array $questionConfig,
        SurveyConfiguration $surveyConfiguration,
        array $dataPrefix = []
    ): iterable {
        foreach ($this->parsers as $parser) {
            yield from $parser->parse($root, $questionConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
