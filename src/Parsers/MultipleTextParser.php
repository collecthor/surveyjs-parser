<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

final class MultipleTextParser implements ElementParserInterface
{
    use ParserHelpers;
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $itemNames = [];
        /** @var list<array<string, string>> $items */
        $items = $questionConfig['items'];
        foreach ($items as $item) {
            $fullPath = [...$dataPrefix, $this->extractValueName($questionConfig), $this->extractName($item)];
            $itemName = implode('.', $fullPath);
            if (in_array($itemName, $itemNames, true)) {
                throw new \RuntimeException("Duplicate question code: {$itemName}");
            }
            yield new OpenTextVariable($itemName, $this->extractTitles($item), $fullPath);
            $itemNames[] = $itemName;
        }
    }
}
