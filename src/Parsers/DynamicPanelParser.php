<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;

final class DynamicPanelParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        /** @var array<string, mixed> $questionConfig */
        $maxCount = $questionConfig['maxPanelCount'] ?? 10;
        $valueName = $this->extractValueName($questionConfig);
        /** @var list<mixed> $elements */
        $elements = $questionConfig['templateElements'] ?? [];
        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($elements as $element) {
                /** @var non-empty-array<string, mixed> $element */
                yield from $root->parse($root, $element, $surveyConfiguration, [...$dataPrefix, $valueName, (string)$i]);
            }
        }
    }
}
