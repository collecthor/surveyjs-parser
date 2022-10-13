<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;

class PanelParser implements ElementParserInterface
{
    use ParserHelpers;
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        foreach ($this->extractOptionalArray($questionConfig, 'elements') ?? [] as $elementConfig) {
            /** @phpstan-ignore-next-line  */
            yield from $root->parse($root, $elementConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
