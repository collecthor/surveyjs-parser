<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

final class MatrixParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $valueName = $this->extractValueName($questionConfig);
        /** @var array{columns: list<string|array<string, string>>, rows: list<string|array<string, string>> } $questionConfig */
        $answers = $this->extractChoices($questionConfig['columns'], $surveyConfiguration);

        /** @var array{value: string, text:string|array<string, string>} $rows */
        $rows = $questionConfig['rows'];

        foreach ($rows as $row) {
            if (is_string($row)) {
                $row = [
                    'text' => $row,
                    'value' => $row,
                ];
            }
            /** @var array{value: string, text: string|array<string,string>} $row */
            $formattedTitles = [];

            if (is_array($row['text'])) {
                $suffixes = [];
                foreach ($row['text'] as $locale => $label) {
                    $suffixes[$locale] = " - {$label}";
                }
                $formattedTitles = $this->formatLocalizedStrings($titles, suffix:$suffixes);
            } else {
                $formattedTitles = $this->formatLocalizedStrings($titles, suffix:" - {$row['text']}");
            }

            yield new SingleChoiceVariable(
                "{$valueName}.{$row['value']}",
                $formattedTitles,
                $answers,
                [...$dataPrefix, $valueName, $row['value']]
            );
        }
    }
}
