<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

class SingleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function __construct(
        private CommentParser $commentParser
    ) {
    }

    public function parse(
        ElementParserInterface $parent,
        array $questionConfig,
        SurveyConfiguration $surveyConfiguration,
        array $dataPrefix = []
    ): iterable {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig)]);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        // Parse the answer options.
        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'), $surveyConfiguration);


        yield new SingleChoiceVariable($name, $titles, $choices, $dataPath);

        // Check if we have a comment field.
        yield from $this->commentParser->parse($parent, $questionConfig, $surveyConfiguration, $dataPrefix);
    }

    /**
     * @param list<mixed>  $choices
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
