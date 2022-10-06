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
        $titles = $this->extractTitles($questionConfig);
        $valueName = $this->extractValueName($questionConfig);
        /** @var array{columns: list<string|array<string, string>>, rows: list<string|array<string, string>> } $questionConfig */
        $answers = $this->extractChoices($questionConfig['columns']);

        /** @var array{value: string, text:string|array<string, string>} $rows */
        $rows = $questionConfig['rows'];

        foreach ($rows as $row) {
            if (is_string($row)) {
                $row = [
                    'text' => $row,
                    'value' => $row,
                ];
            }

            yield new SingleChoiceVariable(
                "{$valueName}.{$row['value']}",
                $this->arrayFormat($titles, " - ", $row['text']),
                $answers,
                [...$dataPrefix, $valueName, $row['value']]
            );
        }
    }
}
