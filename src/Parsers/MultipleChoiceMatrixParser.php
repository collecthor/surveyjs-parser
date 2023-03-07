<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use function is_string;

final class MultipleChoiceMatrixParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig);
        $valueName = $this->extractValueName($questionConfig);

        /** @var list<string|array{value: string, text:string|array<string, string>}> $rows */
        $rows = $this->extractArray($questionConfig, 'rows');
        /** @var list<array<string, mixed>> $columns */
        $columns = $this->extractArray($questionConfig, 'columns');

        $defaultChoices = $this->extractOptionalArray($questionConfig, 'choices');


        foreach ($rows as $row) {
            $rowName = !is_string($row) ? $row['value'] : $row;
            $rowTitle = !is_string($row) ? $row['text'] : $row;
            foreach ($columns as $column) {
                $columnTitle = $this->extractTitles($column);
                $title = $this->arrayFormat($titles, " - ", $rowTitle, " - ", $columnTitle);

                $prefix = [...$dataPrefix, $valueName, $rowName];

                $columnQuestion = $column;
                if (isset($columnQuestion['showNoneItem']) && $columnQuestion['showNoneItem'] === true) {
                    $columnQuestion['hasNone'] = true;
                }
                $columnQuestion['title'] = $title;
                $columnQuestion['type'] = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
                $columnQuestion['choices'] = $columnQuestion['choices'] ?? $defaultChoices;
                yield from $root->parse($root, $columnQuestion, $surveyConfiguration, $prefix);
            }
        }
    }
}
