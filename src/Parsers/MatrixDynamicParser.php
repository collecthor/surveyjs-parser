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

class MatrixDynamicParser implements ElementParserInterface
{
    use ParserHelpers;
    public function __construct(private string $rowLabel)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        /** @var non-empty-array<int, ValueOptionInterface> $answers */
        $answers = [];
        $choices = [];
        if (isset($questionConfig['choices'])) {
            $choices = (array) $questionConfig['choices'];
        }
        foreach ($choices as $answer) {
            if (is_scalar($answer)) {
                $answer = [
                    'value' => $answer,
                    'text' => [
                        'default' => (string) $answer,
                    ],
                ];
            }
            /** @var array{value: string|int, text:array<string, string>} $answer */
            $answers[] = new StringValueOption(
                (string)$answer['value'],
                $this->extractLocalizedTexts($answer['text'], $surveyConfiguration)
            );
        }

        $rowLimit = $questionConfig['maxRowCount'] ?? 10;

        $valueName = $this->extractValueName($questionConfig);
        /** @var array<string, mixed> $column */
        foreach ((array)$questionConfig['columns'] as $column) {
            /** @var string $cellType */
            $cellType = $column['cellType'] ?? $questionConfig['cellType'] ?? 'dropdown';
            switch ($cellType) {
                case 'text':
                    for ($r = 0; $r < $rowLimit; $r++) {
                        $path = [...$dataPrefix, $valueName, (string)$r, $this->extractValueName($column)];
                        $name = implode('.', [...$dataPrefix, $questionConfig['name'], $r, $column['name']]);
                        $titles = $this->formatLocalizedStrings($this->extractTitles($column, $surveyConfiguration), suffix: " ({$this->rowLabel} {$r})");
                        yield new OpenTextVariable($name, $titles, $path);
                    }
                    break;
                case 'dropdown':
                case 'radiogroup':
                    for ($r = 0; $r < $rowLimit; $r++) {
                        $path = [...$dataPrefix, $valueName, (string)$r, $this->extractValueName($column)];
                        $name = implode('.', [...$dataPrefix, $questionConfig['name'], $r, $column['name']]);
                        $titles = $this->formatLocalizedStrings($this->extractTitles($column, $surveyConfiguration), suffix: " ({$this->rowLabel} {$r})");
                        yield new SingleChoiceVariable($name, $titles, $answers, $path);
                    }
                    break;
                case 'checkbox':
                    for ($r = 0; $r < $rowLimit; $r++) {
                        $path = [...$dataPrefix, $valueName, (string)$r, $this->extractValueName($column)];
                        $name = implode('.', [...$dataPrefix, $questionConfig['name'], $r, $column['name']]);
                        $titles = $this->formatLocalizedStrings($this->extractTitles($column, $surveyConfiguration), suffix: " ({$this->rowLabel} {$r})");
                        yield new MultipleChoiceVariable($name, $titles, $answers, $path);
                    }
                    break;
                default:
                    throw new \Exception("Unknown cell type: {$cellType}");
            }
        }
    }
}
