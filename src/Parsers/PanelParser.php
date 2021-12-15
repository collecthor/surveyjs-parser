<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

class PanelParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if (!isset($questionConfig['elements'])) {
            throw new \InvalidArgumentException('Elements key must be set and non-null');
        }
        if (!is_iterable($questionConfig['elements'])) {
            throw new \InvalidArgumentException('Elements must be iterable, got: ' . print_r($questionConfig['elements'], true));
        }
        foreach ($questionConfig['elements'] as $elementConfig) {
            yield from $root->parse($root, $elementConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
