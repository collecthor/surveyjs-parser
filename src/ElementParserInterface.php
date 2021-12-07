<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;

interface ElementParserInterface
{
    /**
     * @param ElementParserInterface $parent This allows an element parser to parse subtypes that it itself doesn't know.
     * @phpstan-param non-empty-array<string, mixed> $questionConfig
     * @param list<string> $dataPrefix
     * @return iterable<VariableInterface>
     */
    public function parse(ElementParserInterface $parent, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable;
}
