<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

use function Collecthor\SurveyjsParser\Helpers\arrayFormat;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalName;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class MultipleTextParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $itemNames = [];
        $questionTitles = extractTitles($questionConfig);
        foreach (extractOptionalArray($questionConfig, 'items') ?? [] as $item) {
            if (!is_array($item)) {
                throw new \InvalidArgumentException("Item must be an array, got: " . print_r($item, true));
            }

            if ($item === []) {
                continue;
            }

            $fullPath = [...$dataPrefix, extractValueName($questionConfig), extractOptionalName($item)];
            $itemName = implode('.', $fullPath);
            if (in_array($itemName, $itemNames, true)) {
                throw new \RuntimeException("Duplicate question code: {$itemName}");
            }

            $title = arrayFormat($questionTitles, " - ", extractTitles($item));
            yield new OpenTextVariable(
                name: $itemName,
                dataPath: $fullPath,
                titles: $title,
                rawConfiguration: $questionConfig
            );
            $itemNames[] = $itemName;
        }
    }
}
