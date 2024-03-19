<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;

interface ElementParserInterface
{
    /**
     * @param ElementParserInterface $root This allows an element parser to parse subtypes that it itself doesn't know.
     * @phpstan-param non-empty-array<mixed> $questionConfig
     * @param list<string> $dataPrefix
     * @return iterable<VariableInterface | DeferredVariable>
     */
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable;
}
