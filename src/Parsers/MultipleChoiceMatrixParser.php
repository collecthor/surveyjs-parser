<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

use function Collecthor\SurveyjsParser\Helpers\arrayFormat;
use function Collecthor\SurveyjsParser\Helpers\extractLocalizedTexts;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;
use function is_string;

final readonly class MultipleChoiceMatrixParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = extractTitles($questionConfig);
        $valueName = extractValueName($questionConfig);

        /** @var list<string|array{value: string, text:string|array<string, string>}> $rows */
        $rows = extractOptionalArray($questionConfig, 'rows') ?? [];
        /** @var list<array<string, mixed>> $columns */
        $columns = extractOptionalArray($questionConfig, 'columns') ?? [];

        $defaultChoices = extractOptionalArray($questionConfig, 'choices');


        foreach ($rows as $row) {
            if (is_string($row)) {
                $rowName = $rowTitles = $row;
            } else {
                $rowName = $row['value'];
                $rowTitles = extractLocalizedTexts($row, defaults: ['default' => $row['value']]);
            }
            foreach ($columns as $column) {
                $columnTitle = extractTitles($column);
                $title = arrayFormat($titles, " - ", $rowTitles, " - ", $columnTitle);

                $prefix = [...$dataPrefix, $valueName, $rowName];

                $columnQuestion = $column;
                $columnQuestion['title'] = $title;
                $columnQuestion['type'] = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
                $columnQuestion['choices'] = $columnQuestion['choices'] ?? $defaultChoices;
                yield from $root->parse($root, $columnQuestion, $surveyConfiguration, $prefix);
            }
        }
    }
}
