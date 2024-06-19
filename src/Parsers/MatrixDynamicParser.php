<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;

final readonly class MatrixDynamicParser implements ElementParserInterface
{
    use ParserHelpers;
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
        foreach ($this->extractOptionalArray($questionConfig, 'choices') ?? [] as $answer) {
            if (is_scalar($answer)) {
                $answer = [
                    'value' => $answer,
                    'text' => (string)$answer,
                ];
            }
            $answers[] = $answer;
        }

        $questionTitles = $this->extractTitles($questionConfig);

        $rowLimit = $questionConfig['maxRowCount'] ?? 10;

        $valueName = $this->extractValueName($questionConfig);
        /** @var array<string, mixed> $column */
        foreach ((array)$questionConfig['columns'] as $column) {
            /** @var string $columnName */
            $columnName = $column['name'];
            for ($r = 0; $r < $rowLimit; $r++) {
                $rowConfig = $column;
                $rowConfig['type'] = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
                $rowConfig['choices'] = $this->extractOptionalArray($column, 'choices') ?? $answers;
                $rowConfig['title'] = $this->arrayFormat($questionTitles, ' ', $columnName, ' ', $this->rowLabels, " $r");
                $rowConfig['valueName'] = $columnName;
                yield from $root->parse(root: $root, questionConfig: $rowConfig, surveyConfiguration: $surveyConfiguration, dataPrefix: [...$dataPrefix, $valueName, (string)$r]);
            }
        }
    }
}
