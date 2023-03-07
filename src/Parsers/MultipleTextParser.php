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
        $questionTitles = $this->extractTitles($questionConfig);
        foreach ($this->extractOptionalArray($questionConfig, 'items') ?? [] as $item) {
            if (!is_array($item)) {
                throw new \InvalidArgumentException("Item must be an array, got: " . print_r($item, true));
            }

            if ($item === []) {
                continue;
            }

            $fullPath = [...$dataPrefix, $this->extractValueName($questionConfig), $this->extractOptionalName($item)];
            $itemName = implode('.', $fullPath);
            if (in_array($itemName, $itemNames, true)) {
                throw new \RuntimeException("Duplicate question code: {$itemName}");
            }

            $title = $this->arrayFormat($questionTitles, " - ", $this->extractTitles($item));
            yield new OpenTextVariable($itemName, $title, $fullPath);
            $itemNames[] = $itemName;
        }
    }
}
