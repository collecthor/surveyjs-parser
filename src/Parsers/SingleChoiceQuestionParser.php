<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Values\StringValueOption;

class SingleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(
        ElementParserInterface $parent,
        array $questionConfig,
        SurveyConfiguration $surveyConfiguration,
        array $dataPrefix = []
    ): iterable {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, $questionConfig['name']]);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        // Parse the answer options.
        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'), $surveyConfiguration);



        return [];
    }

    /**
     * @param list<mixed>  $choices
     * @param SurveyConfiguration $surveyConfiguration
     * @return array<ValueOptionInterface>
     */
    private function extractChoices(array $choices, SurveyConfiguration $surveyConfiguration): array
    {
        $result = [];
        foreach ($choices as $choice) {
            if (is_array($choice)) {
                $value = $choice['value'];
                $displayValues = $this->extractLocalizedTexts($choice, $surveyConfiguration);
            } elseif (is_string($choice)) {
                $value = $choice;
                $displayValues = [];
            } else {
                throw new \InvalidArgumentException("Each choice must be a string or an array with keys value and text");
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
