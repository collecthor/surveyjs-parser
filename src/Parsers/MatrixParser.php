<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

use function Collecthor\SurveyjsParser\Helpers\arrayFormat;
use function Collecthor\SurveyjsParser\Helpers\extractChoices;
use function Collecthor\SurveyjsParser\Helpers\extractLocalizedTexts;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class MatrixParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = extractTitles($questionConfig);
        $valueName = extractValueName($questionConfig);
        $answers = extractChoices($questionConfig['columns'] ?? null);
        if ($answers === []) {
            throw new \InvalidArgumentException("Matrix questions must have columns");
        }

        /** @var list<string|array{value?: string, text?:string|array<string, string>}> $rows */
        $rows = $questionConfig['rows'] ?? [];
        if ($rows === []) {
            throw new ParseError("Matrix questions must have rows");
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
                if (!is_scalar($row['value'] ?? null)) {
                    throw new \InvalidArgumentException("Matrix rows MUST contain a 'value' key with a scalar value");
                }

                $rowTexts = extractLocalizedTexts($row, defaults: ['default' => (string) $row['value']]);

                yield new SingleChoiceVariable(
                    name: "{$valueName}.{$row['value']}",
                    titles: arrayFormat($titles, " - ", $rowTexts),
                    options: $answers,
                    dataPath: [...$dataPrefix, $valueName, $row['value']],
                    rawConfiguration: $questionConfig
                );
            }
        }
    }
}
