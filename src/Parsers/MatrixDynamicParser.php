<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

final class MatrixDynamicParser implements ElementParserInterface
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
        
        /** @var non-empty-array<int, ValueOptionInterface> $answers */
        $answers = [];
        foreach ((array) $questionConfig['choices'] ?? [] as $answer) {
            if (is_scalar($answer)) {
                $answer = [
                    'value' => $answer,
                    'text' => (string)$answer,
                ];
            }
            $answers[] = $answer;
        }

        $questionTitles = $this->extractTitles($questionConfig, $surveyConfiguration);

        $rowLimit = $questionConfig['maxRowCount'] ?? 10;

        $valueName = $this->extractValueName($questionConfig);
        /** @var array<string, mixed> $column */
        foreach ((array)$questionConfig['columns'] as $column) {
            for ($r = 0; $r < $rowLimit; $r++) {
                $rowConfig = $column;
                $rowConfig['type'] = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
                $rowConfig['choices'] = $column['choices'] ?? $answers ?? [];
                $rowConfig['name'] = $this->arrayFormat($surveyConfiguration, $questionTitles, ' - ', $column['name'], ' ', $this->rowLabels, " $r ");
                yield from $root->parse($root, $rowConfig, $surveyConfiguration, [...$dataPrefix, $valueName, $column['name'], (string)$r]);
            }
        }
    }
}
