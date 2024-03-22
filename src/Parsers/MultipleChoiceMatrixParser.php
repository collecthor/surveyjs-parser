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
        $rows = $this->extractOptionalArray($questionConfig, 'rows') ?? [];
        /** @var list<array<string, mixed>> $columns */
        $columns = $this->extractOptionalArray($questionConfig, 'columns') ?? [];

        $defaultChoices = $this->extractOptionalArray($questionConfig, 'choices');


        foreach ($rows as $row) {
            if (is_string($row)) {
                $rowName = $rowTitles = $row;
            } else {
                $rowName = $row['value'];
                $rowTitles = $this->extractLocalizedTexts($row, defaults: ['default' => $row['value']]);
            }
            foreach ($columns as $column) {
                $columnTitle = $this->extractTitles($column);
                $title = $this->arrayFormat($titles, " - ", $rowTitles, " - ", $columnTitle);

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
