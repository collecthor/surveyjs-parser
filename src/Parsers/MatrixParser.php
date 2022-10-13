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
        $answers = $this->extractChoices($questionConfig['columns'] ?? null);
        if ($answers === []) {
            throw new \InvalidArgumentException("Matrix questions must have columns");
        }

        /** @var list<string|array{value?: string, text?:string|array<string, string>}> $rows */
        $rows = $questionConfig['rows'] ?? [];
        if ($rows === []) {
            // This is a matrix rendered as a single single choice variable
            yield new SingleChoiceVariable(
                "$valueName",
                $titles,
                $answers,
                [...$dataPrefix, $valueName]
            );
        } else {
            foreach ($rows as $row) {
                if (is_string($row)) {
                    $row = [
                        'value' => $row,
                    ];
                }

                if ($row === []) {
                    continue;
                }
                if (!isset($row['value']) || !is_string($row['value'])) {
                    throw new \InvalidArgumentException("Matrix rows MUST contain a 'value' key with a string value");
                }

                $rowTexts = $this->extractLocalizedTexts($row, defaults: ['default' => $row['value']]);

                yield new SingleChoiceVariable(
                    "{$valueName}.{$row['value']}",
                    $this->arrayFormat($titles, " - ", $rowTexts),
                    $answers,
                    [...$dataPrefix, $valueName, $row['value']]
                );
            }
        }
    }
}
