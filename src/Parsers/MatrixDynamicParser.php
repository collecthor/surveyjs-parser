<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

use function Collecthor\SurveyjsParser\Helpers\arrayFormat;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class MatrixDynamicParser implements ElementParserInterface
{
    /**
     *
     * @param array<string, string> $rowLabels
     * @return void
     */
    public function __construct(private array $rowLabels)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $answers = [];
        foreach (extractOptionalArray($questionConfig, 'choices') ?? [] as $answer) {
            if (is_scalar($answer)) {
                $answer = [
                    'value' => $answer,
                    'text' => (string)$answer,
                ];
            }
            $answers[] = $answer;
        }

        $questionTitles = extractTitles($questionConfig);

        $rowLimit = $questionConfig['maxRowCount'] ?? 10;

        $valueName = extractValueName($questionConfig);
        /** @var array<string, mixed> $column */
        foreach ((array)$questionConfig['columns'] as $column) {
            /** @var string $columnName */
            $columnName = $column['name'];
            for ($r = 0; $r < $rowLimit; $r++) {
                $rowConfig = $column;
                $rowConfig['type'] = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
                $rowConfig['choices'] = extractOptionalArray($column, 'choices') ?? $answers;
                $rowConfig['title'] = arrayFormat($questionTitles, ' ', $columnName, ' ', $this->rowLabels, " $r");
                $rowConfig['valueName'] = $columnName;
                yield from $root->parse(root: $root, questionConfig: $rowConfig, surveyConfiguration: $surveyConfiguration, dataPrefix: [...$dataPrefix, $valueName, (string)$r]);
            }
        }
    }
}
