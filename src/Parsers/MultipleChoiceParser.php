<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;

class MultipleChoiceParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = $this->extractName($questionConfig);

        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'), $surveyConfiguration);

        // Check if we need to add options for `hasNone` or `hasOther`
        if ($this->extractOptionalBoolean($questionConfig, 'hasNone') ?? false) {
            $choices[] = new StringValueOption('none', $this->extractLocalizedTexts($questionConfig, $surveyConfiguration, 'noneText'));
        }

        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false) {
            $choices[] = new StringValueOption('other', $this->extractLocalizedTexts($questionConfig, $surveyConfiguration, 'otherText'));
        }

        yield new MultipleChoiceVariable($name, $titles, $choices, $dataPath);
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }

    /**
     * @param array<mixed>  $choices
     * @param SurveyConfiguration $surveyConfiguration
     * @phpstan-return non-empty-list<ValueOptionInterface>
     */
    private function extractChoices(array $choices, SurveyConfiguration $surveyConfiguration): array
    {
        if (!array_is_list($choices) || $choices === []) {
            throw new \InvalidArgumentException("Choices must be a non empty list");
        }
        $result = [];
        foreach ($choices as $choice) {
            if (is_array($choice) && isset($choice['value'], $choice['text'])) {
                $value = $choice['value'];
                $displayValues = $this->extractLocalizedTexts($choice, $surveyConfiguration);
            } elseif (is_string($choice) || is_int($choice)) {
                $value = $choice;
                $displayValues = [];
            } else {
                throw new \InvalidArgumentException("Each choice must be a string or int or an array with keys value and text");
            }

            if (is_int($value)) {
                $result[] = new IntegerValueOption($value, $displayValues);
            } elseif (is_scalar($value)) {
                $result[] = new StringValueOption((string) $value, $displayValues);
            } else {
                throw new \InvalidArgumentException('Values must be scalar, got: ' . print_r($choice, true));
            }
        }

        return $result;
    }
}
