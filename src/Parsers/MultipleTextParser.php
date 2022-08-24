<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

class MultipleTextParser implements ElementParserInterface
{
    use ParserHelpers;
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $itemNames = [];
        foreach ($questionConfig['items'] as $item) {
            $fullPath = [...$dataPrefix, $this->extractValueName($questionConfig), $item['name']];
            $itemName = implode('.', $fullPath);
            if (in_array($itemName, $itemNames)) {
                throw new \RuntimeException("Duplicate question code: {$itemName}");
            }
            yield new OpenTextVariable($itemName, $this->extractTitles($item, $surveyConfiguration), $fullPath);
            $itemNames[] = $itemName;
        }
    }
}
