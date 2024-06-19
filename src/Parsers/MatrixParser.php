<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

final readonly class MatrixParser implements ElementParserInterface
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

                $rowTexts = $this->extractLocalizedTexts($row, defaults: ['default' => (string) $row['value']]);

                yield new SingleChoiceVariable(
                    name: "{$valueName}.{$row['value']}",
                    titles: $this->arrayFormat($titles, " - ", $rowTexts),
                    options: $answers,
                    dataPath: [...$dataPrefix, $valueName, $row['value']],
                    rawConfiguration: $questionConfig
                );
            }
        }
    }
}
