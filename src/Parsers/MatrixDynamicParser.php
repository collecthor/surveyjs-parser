<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

class MatrixDynamicParser implements ElementParserInterface
{
    use ParserHelpers;
    public function __construct(private string $rowLabel)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $answers = [];
        foreach ($questionConfig['choices'] ?? [] as $answer) {
            if (is_scalar($answer)) {
                $answer = [
                    'value' => $answer,
                    'text' => $answer
                ];
            }
            $answers[] = new StringValueOption($answer['value'], $this->extractLocalizedTexts($answer, $surveyConfiguration));
        }

        $rowLimit = $questionConfig['maxRowCount'] ?? 10;

        $valueName = $this->extractValueName($questionConfig);

        foreach ($questionConfig['columns'] as $column) {
            $cellType = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
            switch ($cellType) {
                case 'text':
                    for ($r = 0; $r < $rowLimit; $r++) {
                        $path = [...$dataPrefix, $valueName, $r, $column['valueName'] ?? $column['name']];
                        $name = implode('.', [...$dataPrefix, $questionConfig['name'], $r, $column['name']]);
                        $titles = $this->formatLocalizedStrings($this->extractTitles($column, $surveyConfiguration), suffix:" (Row {$r})");
                        yield new OpenTextVariable($name, $titles, $path);
                    }
                    break;
                case 'dropdown':
                case 'radiogroup':
                    for ($r = 0; $r < $rowLimit; $r++) {
                        $path = [...$dataPrefix, $valueName, $r, $column['valueName'] ?? $column['name']];
                        $name = implode('.', [...$dataPrefix, $questionConfig['name'], $r, $column['name']]);
                        $titles = $this->formatLocalizedStrings($this->extractTitles($column, $surveyConfiguration), suffix:" (Row {$r})");
                        yield new SingleChoiceVariable($name, $titles, $answers, $path);
                    }
                    break;
                case 'checkbox':
                    for ($r = 0; $r < $rowLimit; $r++) {
                        $path = [...$dataPrefix, $valueName, $r, $column['valueName'] ?? $column['name']];
                        $name = implode('.', [...$dataPrefix, $questionConfig['name'], $r, $column['name']]);
                        $titles = $this->formatLocalizedStrings($this->extractTitles($column, $surveyConfiguration), suffix:" (Row {$r})");
                        yield new MultipleChoiceVariable($name, $titles, $answers, $path);
                    }
                    break;
                default:
                    throw new \Exception("Unknown cell type: {$cellType}");

            }
        }
    }
}
