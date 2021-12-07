<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

/**
 * @codeCoverageIgnore
 */
class DummyParser implements ElementParserInterface
{
    public function parse(
        ElementParserInterface $parent,
        array $questionConfig,
        SurveyConfiguration $surveyConfiguration,
        array $dataPrefix = []
    ): iterable {
        return [];
    }
}
